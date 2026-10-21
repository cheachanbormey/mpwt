<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Office;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 03 - the generic admin module pages.
 */
class ModuleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        return $admin;
    }

    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_can_create_a_department(): void
    {
        $this->admin();

        $this->post('/admin/departments', [
            'code' => 'DEP-100',
            'name' => 'នាយកដ្ឋានសាកល្បង',
        ])->assertRedirect(route('admin.departments.index'));

        $this->assertDatabaseHas('departments', [
            'code' => 'DEP-100',
            'name' => 'នាយកដ្ឋានសាកល្បង',
        ]);
    }

    public function test_a_department_code_must_stay_unique(): void
    {
        $this->admin();
        Department::factory()->create(['code' => 'DEP-100']);

        $this->post('/admin/departments', [
            'code' => 'DEP-100',
            'name' => 'មួយទៀត',
        ])->assertSessionHasErrors('code');
    }

    public function test_an_admin_can_update_and_delete_a_department(): void
    {
        $this->admin();
        $department = Department::factory()->create();

        $this->put("/admin/departments/{$department->id}", [
            'code' => 'DEP-200',
            'name' => 'ឈ្មោះថ្មី',
        ])->assertRedirect(route('admin.departments.index'));

        $this->assertDatabaseHas('departments', ['id' => $department->id, 'code' => 'DEP-200']);

        $this->delete("/admin/departments/{$department->id}")
            ->assertRedirect(route('admin.departments.index'));

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_a_leave_type_still_in_use_cannot_be_deleted(): void
    {
        $this->admin();
        $leaveType = LeaveType::factory()->create();

        LeaveRequest::factory()->create([
            'leave_type_id' => $leaveType->id,
        ]);

        $this->from('/admin/leave_types')
            ->delete("/admin/leave_types/{$leaveType->id}")
            ->assertRedirect('/admin/leave_types')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('leave_types', ['id' => $leaveType->id]);
    }

    public function test_a_department_is_removed_together_with_its_offices(): void
    {
        $this->admin();
        $department = Department::factory()->create();
        Office::factory()->create(['department_id' => $department->id]);

        // offices.department_id cascades, so this must succeed.
        $this->delete("/admin/departments/{$department->id}")
            ->assertRedirect(route('admin.departments.index'));

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
        $this->assertDatabaseCount('offices', 0);
    }

    public function test_only_declared_fields_are_written(): void
    {
        $this->admin();

        $this->post('/admin/departments', [
            'code' => 'DEP-300',
            'name' => 'សុវត្ថិភាព',
            'id' => 9999,
            'created_at' => '1999-01-01 00:00:00',
        ])->assertRedirect(route('admin.departments.index'));

        $department = Department::query()->where('code', 'DEP-300')->firstOrFail();

        $this->assertNotSame(9999, $department->id);
        $this->assertNotSame('1999-01-01', $department->created_at->format('Y-m-d'));
    }

    /*
    |--------------------------------------------------------------------------
    | Search and filters
    |--------------------------------------------------------------------------
    */

    public function test_the_list_can_be_searched(): void
    {
        $this->admin();
        Department::factory()->create(['code' => 'DEP-AAA', 'name' => 'ផ្ទាំងមួយ']);
        Department::factory()->create(['code' => 'DEP-BBB', 'name' => 'ផ្ទាំងពីរ']);

        $this->get('/admin/departments?q='.urlencode('ផ្ទាំងពីរ'))
            ->assertOk()
            ->assertSee('ផ្ទាំងពីរ')
            ->assertDontSee('ផ្ទាំងមួយ');
    }

    public function test_the_leave_request_list_accepts_a_date_range(): void
    {
        $this->admin();
        $staff = Staff::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $inside = LeaveRequest::factory()->create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-07',
            'reason' => 'សំឡេងក្នុងចន្លោះ',
        ]);

        $outside = LeaveRequest::factory()->create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-11-05',
            'end_date' => '2026-11-07',
            'reason' => 'សំឡេងក្រៅចន្លោះ',
        ]);

        $response = $this->get('/admin/leave_requests?from=2026-10-01&to=2026-10-31');

        $response->assertOk();
        $response->assertViewHas('rows', fn ($rows) => $rows->count() === 1 && $rows->first()->is($inside));
        $response->assertDontSee($outside->reason);
    }

    public function test_the_leave_request_list_can_be_filtered_by_status(): void
    {
        $this->admin();
        $staff = Staff::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $pending = LeaveRequest::factory()->create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'status' => LeaveRequest::STATUS_PENDING,
            'reason' => 'សំឡេងកំពុងរង់ចាំ',
        ]);

        LeaveRequest::factory()->create([
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'status' => LeaveRequest::STATUS_APPROVED,
            'reason' => 'សំឡេងបានអនុម័ត',
        ]);

        $this->get('/admin/leave_requests?status=pending')
            ->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->count() === 1 && $rows->first()->is($pending));
    }

    /*
    |--------------------------------------------------------------------------
    | Leave status stays read-only for administrators too
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_cannot_change_the_status_of_a_leave_request(): void
    {
        $this->admin();
        $request = LeaveRequest::factory()->create([
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        $this->put("/admin/leave_requests/{$request->id}", [
            'staff_id' => $request->staff_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'total_days' => 2,
            'reason' => 'មូលដ្ឋានត្រូវបានកែសម្រួល',
            'status' => LeaveRequest::STATUS_APPROVED,
        ])->assertRedirect(route('admin.leave_requests.index'));

        $request->refresh();

        $this->assertSame(LeaveRequest::STATUS_PENDING, $request->status);
        $this->assertSame('មូលដ្ឋានត្រូវបានកែសម្រួល', $request->reason);
    }

    public function test_an_admin_cannot_create_a_leave_request_that_is_already_approved(): void
    {
        $this->admin();
        $staff = Staff::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $this->post('/admin/leave_requests', [
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'total_days' => 2,
            'reason' => 'សំឡេងដែលព្យាយាមបញ្ចូលជាមួយ',
            'status' => LeaveRequest::STATUS_APPROVED,
        ])->assertRedirect(route('admin.leave_requests.index'));

        $this->assertDatabaseHas('leave_requests', [
            'staff_id' => $staff->id,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);
    }

    public function test_the_leave_request_form_renders_the_status_as_read_only(): void
    {
        $this->admin();

        $response = $this->get('/admin/leave_requests/create');

        $response->assertOk();
        $response->assertSee('ស្ថានភាព (មិនអាចកែប្រែ)');
    }

    /*
    |--------------------------------------------------------------------------
    | Employment statuses
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_can_manage_employment_statuses(): void
    {
        $this->admin();

        $this->post('/admin/employment_statuses', [
            'code' => 'borrowed',
            'name' => 'ស្មកសិកធន៍',
            'sort_order' => 9,
            'is_active' => '1',
        ])->assertRedirect(route('admin.employment_statuses.index'));

        $this->assertDatabaseHas('employment_statuses', ['code' => 'borrowed']);
    }

    public function test_an_unknown_module_returns_404(): void
    {
        $this->admin();

        $this->get('/admin/not_a_module')->assertNotFound();
    }
}
