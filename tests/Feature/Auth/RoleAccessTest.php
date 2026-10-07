<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Admin\ModuleController;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Staff;
use App\Models\StaffV2;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 03 - role based access control.
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Administrators
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_can_open_every_management_module(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach (array_keys(ModuleController::registry()) as $module) {
            $this->get("/admin/{$module}")->assertOk();
        }
    }

    public function test_an_admin_can_open_the_create_form_of_every_module(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach (array_keys(ModuleController::registry()) as $module) {
            $this->get("/admin/{$module}/create")->assertOk();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Staff
    |--------------------------------------------------------------------------
    */

    public function test_a_staff_member_is_forbidden_from_every_management_module(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        foreach (array_keys(ModuleController::registry()) as $module) {
            $this->get("/admin/{$module}")->assertForbidden();
            $this->get("/admin/{$module}/create")->assertForbidden();
        }
    }

    public function test_a_staff_member_cannot_write_to_a_management_module(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->post('/admin/departments', [
            'code' => 'DEP-HACK',
            'name' => 'Injected',
        ])->assertForbidden();

        $this->assertDatabaseMissing('departments', ['code' => 'DEP-HACK']);
    }

    public function test_a_guest_is_redirected_away_from_management_modules(): void
    {
        $this->get('/admin/departments')->assertRedirect(route('login'));
        $this->get('/admin/leave_requests')->assertRedirect(route('login'));
    }

    /*
    |--------------------------------------------------------------------------
    | Self-service is open to both roles
    |--------------------------------------------------------------------------
    */

    public function test_staff_self_service_pages_load_for_a_staff_member(): void
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->staff()->create();
        $staff->forceFill(['user_id' => $user->id])->save();

        // The clock runs against the rebuilt staff module, so the account also
        // needs the matching record before the attendance page will open.
        $staffV2 = StaffV2::factory()->create();
        $user->forceFill(['staff_v2_id' => $staffV2->id])->save();

        $this->actingAs($user);

        $this->get(route('staff.profile'))->assertOk();
        $this->get(route('staff.attendance'))->assertOk();
        $this->get(route('staff.leave-requests.index'))->assertOk();
        $this->get(route('staff.leave-requests.create'))->assertOk();
        $this->get(route('staff.meetings.index'))->assertOk();
    }

    public function test_staff_self_service_pages_load_for_an_administrator(): void
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->admin()->create();
        $staff->forceFill(['user_id' => $user->id])->save();
        $staffV2 = StaffV2::factory()->create();
        $user->forceFill(['staff_v2_id' => $staffV2->id])->save();

        $this->actingAs($user);

        $this->get(route('staff.profile'))->assertOk();
        $this->get(route('staff.attendance'))->assertOk();
    }

    public function test_a_user_without_a_staff_record_cannot_open_the_profile_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('staff.profile'))->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Meeting pages must survive at least one registered meeting
    |--------------------------------------------------------------------------
    |
    | Guard against invalid eager loads such as with('pivot'). That mistake
    | stays invisible while the staff member has no meetings at all, because
    | there is nothing to eager load, so the fixture below is deliberate.
    |
    */

    public function test_the_meeting_pages_load_when_the_staff_member_has_a_meeting(): void
    {
        $staff = Staff::factory()->create();
        $user = User::factory()->staff()->create();
        $staff->forceFill(['user_id' => $user->id])->save();

        $meeting = Meeting::factory()->create([
            'meeting_date' => today()->addDays(3),
            'status' => Meeting::STATUS_SCHEDULED,
        ]);

        MeetingParticipant::factory()->create([
            'meeting_id' => $meeting->id,
            'staff_id' => $staff->id,
            'attendance_status' => MeetingParticipant::STATUS_ACCEPTED,
        ]);

        $this->actingAs($user);

        $this->get(route('staff.meetings.index'))
            ->assertOk()
            ->assertViewHas('meetings', function ($paginator) use ($meeting) {
                $paginator->getCollection()->each(function ($row) {
                    // The pivot is supplied by Staff::meetings(), not by an
                    // eager load, and must expose the participation columns.
                    $this->assertNotNull($row->pivot);
                    $this->assertSame(
                        MeetingParticipant::STATUS_ACCEPTED,
                        $row->pivot->attendance_status
                    );
                });

                return $paginator->getCollection()->contains('id', $meeting->id);
            });

        $this->get(route('dashboard'))->assertOk();

        $this->get(route('staff.meetings.show', $meeting))->assertOk();
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation reflects the role
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_navigation_lists_the_management_modules(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('ការគ្រប់គ្រង')
            ->assertSee(route('admin.departments.index'))
            ->assertSee(route('admin.leave_requests.index'));
    }

    public function test_a_staff_navigation_hides_the_management_modules(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->get('/dashboard')
            ->assertOk()
            ->assertDontSee('ការគ្រប់គ្រង')
            ->assertDontSee(route('admin.departments.index'));
    }
}
