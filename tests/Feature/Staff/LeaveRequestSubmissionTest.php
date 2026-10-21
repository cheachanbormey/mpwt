<?php

namespace Tests\Feature\Staff;

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Staff\LeaveRequestController;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Phase 03 - staff self-service for leave requests.
 *
 * The application has no online approval workflow, so a submission can only
 * ever produce a "pending" row.
 */
class LeaveRequestSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function accountWithStaff(string $role = User::ROLE_STAFF): array
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->create(['role' => $role]);
        $staff->forceFill(['user_id' => $user->id])->save();

        return [$user->fresh(), $staff->fresh()];
    }

    public function test_a_request_is_created_as_pending(): void
    {
        [$user, $staff] = $this->accountWithStaff();
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',   // Monday
            'end_date' => '2026-10-07',     // Wednesday
            'reason' => 'ទៅជួលពិគ្រោះកូន',
        ]);

        $response->assertRedirect(route('staff.leave-requests.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'staff_id' => $staff->id,
            'status' => LeaveRequest::STATUS_PENDING,
            'total_days' => 3,
        ]);
    }

    public function test_weekend_days_are_not_counted_as_leave(): void
    {
        [$user] = $this->accountWithStaff();
        $leaveType = LeaveType::factory()->create();

        // Friday to Monday: only Friday and Monday are working days.
        $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-09',
            'end_date' => '2026-10-12',
            'reason' => 'សម្រាប់ការងារផ្ទៃមិន',
        ]);

        $this->assertDatabaseHas('leave_requests', ['total_days' => 2]);
    }

    public function test_a_submission_matching_only_a_weekend_is_refused(): void
    {
        [$user] = $this->accountWithStaff();
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-10',   // Saturday
            'end_date' => '2026-10-11',     // Sunday
            'reason' => 'សុំច្បាប់ថ្ងៃសៅរ៍',
        ]);

        $response->assertSessionHasErrors('start_date');
        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_an_overlapping_request_is_refused(): void
    {
        [$user, $staff] = $this->accountWithStaff();
        $leaveType = LeaveType::factory()->create();

        LeaveRequest::factory()->create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-09',
        ]);

        $response = $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-07',
            'end_date' => '2026-10-08',
            'reason' => 'ជានិងសទ្ទិក',
        ]);

        $response->assertSessionHasErrors('start_date');
        $this->assertDatabaseCount('leave_requests', 1);
    }

    public function test_a_request_always_belongs_to_the_signed_in_person(): void
    {
        [$user] = $this->accountWithStaff();
        $someoneElse = Staff::factory()->create();
        $leaveType = LeaveType::factory()->create();

        // A crafted staff_id must be ignored.
        $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'staff_id' => $someoneElse->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'reason' => 'ការសាកល្បងសុវត្ថិភាព',
        ]);

        $this->assertDatabaseMissing('leave_requests', ['staff_id' => $someoneElse->id]);
    }

    public function test_the_list_shows_only_the_signed_in_person_requests(): void
    {
        [$user, $staff] = $this->accountWithStaff();
        $someoneElse = Staff::factory()->create();

        $mine = LeaveRequest::factory()->create(['staff_id' => $staff->id]);
        $theirs = LeaveRequest::factory()->create(['staff_id' => $someoneElse->id]);

        $response = $this->actingAs($user)->get(route('staff.leave-requests.index'));

        $response->assertOk();
        $response->assertViewHas('leaveRequests', fn ($rows) => $rows->count() === 1 && $rows->first()->is($mine));
        $response->assertDontSee($theirs->reason);
    }

    public function test_a_crafted_status_in_the_request_body_is_ignored(): void
    {
        [$user] = $this->accountWithStaff();
        $leaveType = LeaveType::factory()->create();

        $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'reason' => 'ការសាកល្បងសុវត្ថិភាពស្ថានភាព',
            'status' => LeaveRequest::STATUS_APPROVED,
            'approved_by_staff_id' => 1,
            'approved_at' => Carbon::now()->toDateTimeString(),
        ]);

        $request = LeaveRequest::query()->latest('id')->first();

        $this->assertSame(LeaveRequest::STATUS_PENDING, $request->status);
        $this->assertNull($request->approved_by_staff_id);
        $this->assertNull($request->approved_at);
    }

    public function test_the_submission_form_validates_its_input(): void
    {
        [$user] = $this->accountWithStaff();

        $this->actingAs($user)->post(route('staff.leave-requests.store'), [])
            ->assertSessionHasErrors(['leave_type_id', 'start_date', 'end_date', 'reason']);

        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_the_end_date_cannot_precede_the_start_date(): void
    {
        [$user] = $this->accountWithStaff();
        $leaveType = LeaveType::factory()->create();

        $this->actingAs($user)->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-09',
            'end_date' => '2026-10-05',
            'reason' => 'កាលបរិច្ឆេទបញ្ហា',
        ])->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('leave_requests', 0);
    }

    public function test_a_guest_cannot_submit_a_request(): void
    {
        $leaveType = LeaveType::factory()->create();

        $this->post(route('staff.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'reason' => 'ការសាកល្បងមិនចូលបាន',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('leave_requests', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | No online approval exists
    |--------------------------------------------------------------------------
    */

    public function test_there_is_no_approve_or_reject_route(): void
    {
        $routes = collect(app('router')->getRoutes())
            ->flatMap(fn ($route) => collect($route->methods())->map(fn () => $route))
            ->filter(fn ($route) => str_contains($route->uri(), 'approve')
                || str_contains($route->uri(), 'reject')
                || str_contains((string) $route->getName(), 'approve')
                || str_contains((string) $route->getName(), 'reject'));

        $this->assertCount(0, $routes, 'An approval route exists; approval must stay manual.');
    }

    public function test_no_controller_exposes_an_approval_action(): void
    {
        $controllers = [
            LeaveRequestController::class,
            ModuleController::class,
        ];

        foreach ($controllers as $controller) {
            foreach (get_class_methods($controller) as $method) {
                $this->assertDoesNotMatchRegularExpression(
                    '/^(approve|reject|cancel)/i',
                    $method,
                    "{$controller}::{$method}() looks like an online approval action"
                );
            }
        }
    }
}
