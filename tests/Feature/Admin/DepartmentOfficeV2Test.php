<?php

namespace Tests\Feature\Admin;

use App\Models\DepartmentV2;
use App\Models\LeaveType;
use App\Models\OfficeV2;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 04 - the departments_v2 and offices_v2 modules.
 *
 * These cover the two new tables plus the two actions added to the generic
 * admin engine for them: the detail page and activate / deactivate.
 */
class DepartmentOfficeV2Test extends TestCase
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

    public function test_the_department_list_is_reachable(): void
    {
        $this->admin();

        DepartmentV2::factory()->create(['dep_kh_name' => 'ផ្ទាំងបន្ទាន់ប្រឡង់']);

        $this->get('/admin/departments_v2')
            ->assertOk()
            ->assertSee('ផ្ទាំងបន្ទាន់ប្រឡង់');
    }

    public function test_the_department_list_can_be_searched(): void
    {
        $this->admin();

        DepartmentV2::factory()->create([
            'dep_code' => 'DEP-AAA',
            'dep_kh_name' => 'ផ្ទាំងមួយ',
        ]);

        DepartmentV2::factory()->create([
            'dep_code' => 'DEP-BBB',
            'dep_kh_name' => 'ផ្ទាំងពីរ',
        ]);

        $this->get('/admin/departments_v2?q='.urlencode('ផ្ទាំងពីរ'))
            ->assertOk()
            ->assertSee('ផ្ទាំងពីរ')
            ->assertDontSee('ផ្ទាំងមួយ');
    }

    public function test_an_admin_can_create_a_department(): void
    {
        $this->admin();

        $this->post('/admin/departments_v2', [
            'dep_code' => 'DEP-100',
            'dep_kh_name' => 'នាយកដ្ឋានសាកល្បង',
            'dep_en_name' => 'Testing',
            'status' => DepartmentV2::STATUS_ACTIVE,
        ])->assertRedirect(route('admin.departments_v2.index'));

        $this->assertDatabaseHas('departments_v2', [
            'dep_code' => 'DEP-100',
            'dep_kh_name' => 'នាយកដ្ឋានសាកល្បង',
            'status' => DepartmentV2::STATUS_ACTIVE,
        ]);
    }

    public function test_a_new_department_defaults_to_active(): void
    {
        $this->admin();

        $this->post('/admin/departments_v2', [
            'dep_code' => 'DEP-101',
            'dep_kh_name' => 'ផ្ទាំងលំនាំដើម',
        ])->assertRedirect(route('admin.departments_v2.index'));

        $this->assertDatabaseHas('departments_v2', [
            'dep_code' => 'DEP-101',
            'status' => DepartmentV2::STATUS_ACTIVE,
        ]);
    }

    public function test_a_department_code_must_stay_unique(): void
    {
        $this->admin();

        DepartmentV2::factory()->create(['dep_code' => 'DEP-100']);

        $this->post('/admin/departments_v2', [
            'dep_code' => 'DEP-100',
            'dep_kh_name' => 'មួយទៀត',
            'status' => DepartmentV2::STATUS_ACTIVE,
        ])->assertSessionHasErrors('dep_code');
    }

    public function test_a_department_can_name_its_director(): void
    {
        $this->admin();

        $director = Staff::factory()->create(['name_km' => 'ឯកឧត្តម នាយកផ្ទាំង']);

        $this->post('/admin/departments_v2', [
            'dep_code' => 'DEP-102',
            'dep_kh_name' => 'ផ្ទាំងមាននាយក',
            'director_dep' => $director->id,
            'status' => DepartmentV2::STATUS_ACTIVE,
        ])->assertRedirect(route('admin.departments_v2.index'));

        $department = DepartmentV2::query()->where('dep_code', 'DEP-102')->firstOrFail();

        $this->assertTrue($department->director->is($director));
    }

    public function test_a_department_director_must_be_a_real_staff_member(): void
    {
        $this->admin();

        $this->post('/admin/departments_v2', [
            'dep_code' => 'DEP-103',
            'dep_kh_name' => 'ផ្ទាំងមាននាយកកំណត់ត្រាកន្លោះ',
            'director_dep' => 999999,
            'status' => DepartmentV2::STATUS_ACTIVE,
        ])->assertSessionHasErrors('director_dep');
    }

    public function test_an_admin_can_update_a_department(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();

        $this->put("/admin/departments_v2/{$department->id}", [
            'dep_code' => 'DEP-200',
            'dep_kh_name' => 'ឈ្មោះថ្មី',
            'status' => DepartmentV2::STATUS_ACTIVE,
        ])->assertRedirect(route('admin.departments_v2.index'));

        $this->assertDatabaseHas('departments_v2', [
            'id' => $department->id,
            'dep_code' => 'DEP-200',
            'dep_kh_name' => 'ឈ្មោះថ្មី',
        ]);
    }

    public function test_an_admin_can_delete_a_department(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();

        $this->delete("/admin/departments_v2/{$department->id}")
            ->assertRedirect(route('admin.departments_v2.index'));

        $this->assertDatabaseMissing('departments_v2', ['id' => $department->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show page and subordinate offices
    |--------------------------------------------------------------------------
    */

    public function test_the_department_detail_page_lists_its_subordinate_offices(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create(['dep_kh_name' => 'ផ្ទាំងមានការងារ']);

        $office = OfficeV2::factory()->forDepartment($department)->create([
            'office_name' => 'ការងារមុខវិជ្ជា',
        ]);

        $elsewhere = DepartmentV2::factory()->create();

        OfficeV2::factory()->forDepartment($elsewhere)->create([
            'office_name' => 'ការងារមិនជារបស់ផ្ទាំងនេះ',
        ]);

        $this->get("/admin/departments_v2/{$department->id}")
            ->assertOk()
            ->assertSee('ការងារមុខវិជ្ជា')
            ->assertSee('ការងារក្រោមផ្ទាំង')
            ->assertDontSee('ការងារមិនជារបស់ផ្ទាំងនេះ');

        $this->assertDatabaseHas('offices_v2', ['id' => $office->id]);
    }

    public function test_the_department_detail_page_renders_without_any_offices(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();

        $this->get("/admin/departments_v2/{$department->id}")
            ->assertOk()
            ->assertSee('មិនមានការងារក្រោមផ្ទាំងនៅឡើយ។');
    }

    public function test_deleting_a_department_removes_its_offices(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();
        OfficeV2::factory()->forDepartment($department)->create();

        $this->delete("/admin/departments_v2/{$department->id}")
            ->assertRedirect(route('admin.departments_v2.index'));

        $this->assertDatabaseMissing('departments_v2', ['id' => $department->id]);
        $this->assertDatabaseCount('offices_v2', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / deactivate
    |--------------------------------------------------------------------------
    */

    public function test_a_department_can_be_deactivated_and_activated_again(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();

        $this->patch("/admin/departments_v2/{$department->id}/toggle")
            ->assertRedirect(route('admin.departments_v2.index'));

        $this->assertSame(
            DepartmentV2::STATUS_INACTIVE,
            $department->fresh()->status
        );

        $this->patch("/admin/departments_v2/{$department->id}/toggle")
            ->assertRedirect(route('admin.departments_v2.index'));

        $this->assertSame(
            DepartmentV2::STATUS_ACTIVE,
            $department->fresh()->status
        );
    }

    public function test_an_office_can_be_deactivated(): void
    {
        $this->admin();

        $office = OfficeV2::factory()->create();

        $this->patch("/admin/offices_v2/{$office->id}/toggle")
            ->assertRedirect(route('admin.offices_v2.index'));

        $this->assertSame(OfficeV2::STATUS_INACTIVE, $office->fresh()->status);
    }

    public function test_the_active_scope_only_returns_active_rows(): void
    {
        $this->admin();

        $active = DepartmentV2::factory()->create();
        $inactive = DepartmentV2::factory()->inactive()->create();

        $this->assertTrue(DepartmentV2::query()->active()->pluck('id')->all() === [$active->id]);
        $this->assertTrue(DepartmentV2::query()->inactive()->pluck('id')->all() === [$inactive->id]);

        $this->assertTrue($active->isActive());
        $this->assertFalse($inactive->isActive());
    }

    /*
    |--------------------------------------------------------------------------
    | Offices
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_can_create_an_office(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();

        $this->post('/admin/offices_v2', [
            'dep_id' => $department->id,
            'office_code' => 'OFF-100',
            'office_name' => 'ការិយាល័យសារីរុងថ្មី',
            'location' => 'ខេត្តកំពង',
            'status' => OfficeV2::STATUS_ACTIVE,
        ])->assertRedirect(route('admin.offices_v2.index'));

        $office = OfficeV2::query()->where('office_code', 'OFF-100')->firstOrFail();

        $this->assertTrue($office->department->is($department));
        $this->assertSame('ខេត្តកំពង', $office->location);
    }

    public function test_an_office_must_belong_to_a_real_department(): void
    {
        $this->admin();

        $this->post('/admin/offices_v2', [
            'dep_id' => 999999,
            'office_code' => 'OFF-101',
            'office_name' => 'ការងារកំណត់ត្រាផ្ទាំងកន្លោះ',
            'status' => OfficeV2::STATUS_ACTIVE,
        ])->assertSessionHasErrors('dep_id');
    }

    public function test_an_office_code_must_stay_unique(): void
    {
        $this->admin();

        OfficeV2::factory()->create(['office_code' => 'OFF-100']);

        $this->post('/admin/offices_v2', [
            'dep_id' => DepartmentV2::factory()->create()->id,
            'office_code' => 'OFF-100',
            'office_name' => 'ការងារមួយទៀត',
            'status' => OfficeV2::STATUS_ACTIVE,
        ])->assertSessionHasErrors('office_code');
    }

    public function test_the_office_list_can_be_searched(): void
    {
        $this->admin();

        OfficeV2::factory()->create(['office_name' => 'ការងារមួយ']);
        OfficeV2::factory()->create(['office_name' => 'ការងារពីរ']);

        $this->get('/admin/offices_v2?q='.urlencode('ការងារពីរ'))
            ->assertOk()
            ->assertSee('ការងារពីរ')
            ->assertDontSee('ការងារមួយ');
    }

    public function test_the_office_list_can_be_filtered_by_department(): void
    {
        $this->admin();

        $wanted = DepartmentV2::factory()->create(['dep_kh_name' => 'ផ្ទាំងចង់បាន']);
        $other = DepartmentV2::factory()->create(['dep_kh_name' => 'ផ្ទាំងមិនចង់']);

        $matching = OfficeV2::factory()->forDepartment($wanted)->create([
            'office_name' => 'ការងារត្រូវបាន',
        ]);

        OfficeV2::factory()->forDepartment($other)->create([
            'office_name' => 'ការងារត្រូវបង្កើត',
        ]);

        $this->get("/admin/offices_v2?dep_id={$wanted->id}")
            ->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->count() === 1 && $rows->first()->is($matching))
            ->assertSee('ការងារត្រូវបាន')
            ->assertDontSee('ការងារត្រូវបង្កើត');
    }

    public function test_the_office_list_can_be_filtered_by_status(): void
    {
        $this->admin();

        $active = OfficeV2::factory()->create(['office_name' => 'ការងារសកម្ម']);
        OfficeV2::factory()->inactive()->create(['office_name' => 'ការងារអសកម្ម']);

        $this->get('/admin/offices_v2?status=active')
            ->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->count() === 1 && $rows->first()->is($active))
            ->assertSee('ការងារសកម្ម')
            ->assertDontSee('ការងារអសកម្ម');
    }

    public function test_an_admin_can_update_and_delete_an_office(): void
    {
        $this->admin();

        $office = OfficeV2::factory()->create();

        $this->put("/admin/offices_v2/{$office->id}", [
            'dep_id' => $office->dep_id,
            'office_code' => 'OFF-200',
            'office_name' => 'ការងារកែសម្រួល',
            'status' => OfficeV2::STATUS_ACTIVE,
        ])->assertRedirect(route('admin.offices_v2.index'));

        $this->assertDatabaseHas('offices_v2', [
            'id' => $office->id,
            'office_code' => 'OFF-200',
        ]);

        $this->delete("/admin/offices_v2/{$office->id}")
            ->assertRedirect(route('admin.offices_v2.index'));

        $this->assertDatabaseMissing('offices_v2', ['id' => $office->id]);
    }

    public function test_the_office_detail_page_shows_its_department(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create(['dep_kh_name' => 'ផ្ទាំងមានការងារនេះ']);

        $office = OfficeV2::factory()->forDepartment($department)->create();

        $this->get("/admin/offices_v2/{$office->id}")
            ->assertOk()
            ->assertSee('ផ្ទាំងមានការងារនេះ');
    }

    /*
    |--------------------------------------------------------------------------
    | Isolation from the original modules
    |--------------------------------------------------------------------------
    */

    public function test_the_new_modules_do_not_disturb_the_original_ones(): void
    {
        $this->admin();

        $department = DepartmentV2::factory()->create();
        OfficeV2::factory()->forDepartment($department)->create();

        // The original tables are untouched by writes to the new ones.
        $this->assertDatabaseCount('departments', 0);
        $this->assertDatabaseCount('offices', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Access control
    |--------------------------------------------------------------------------
    */

    public function test_a_signed_out_visitor_is_sent_to_the_login_page(): void
    {
        $this->get('/admin/departments_v2')->assertRedirect(route('login'));
        $this->get('/admin/offices_v2')->assertRedirect(route('login'));
    }

    public function test_a_staff_account_cannot_reach_the_new_modules(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/departments_v2')->assertForbidden();
        $this->get('/admin/offices_v2')->assertForbidden();

        $department = DepartmentV2::factory()->create();

        $this->patch("/admin/departments_v2/{$department->id}/toggle")->assertForbidden();
    }

    public function test_the_new_routes_only_exist_for_the_two_new_modules(): void
    {
        $this->admin();

        // The original departments module never opted in, so it still works
        // as before and gains no detail page.
        $this->get('/admin/departments')->assertOk();

        $leaveType = LeaveType::factory()->create();

        // /admin/leave_types/{id} exists, but only for PUT, PATCH and DELETE,
        // so a GET is refused by the router rather than served.
        $this->get("/admin/leave_types/{$leaveType->id}")->assertStatus(405);
        $this->patch("/admin/leave_types/{$leaveType->id}/toggle")->assertNotFound();
    }
}
