<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\StaffV2;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The administrator-facing attendance overviews.
 *
 * The row-by-row record list is covered by the generic module tests; these
 * cover the two roll-up screens and the form requests behind them.
 */
class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-06 09:00'));
    }

    /*
    |--------------------------------------------------------------------------
    | Reaching the reports
    |--------------------------------------------------------------------------
    */

    public function test_an_administrator_can_open_both_reports(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.attendances.day'))->assertOk();
        $this->get(route('admin.attendances.month'))->assertOk();
    }

    public function test_the_attendance_list_links_to_both_reports(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.attendances.index'))
            ->assertOk()
            ->assertSee(route('admin.attendances.day'), false)
            ->assertSee(route('admin.attendances.month'), false);
    }

    public function test_a_staff_member_cannot_open_the_reports(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->get(route('admin.attendances.day'))->assertForbidden();
        $this->get(route('admin.attendances.month'))->assertForbidden();
    }

    public function test_a_guest_is_sent_to_the_login_screen(): void
    {
        $this->get(route('admin.attendances.day'))->assertRedirect(route('login'));
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    public function test_a_malformed_day_is_rejected_with_a_khmer_message(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->from(route('admin.attendances.day'))
            ->get(route('admin.attendances.day', ['date' => 'not-a-date']))
            ->assertRedirect(route('admin.attendances.day'))
            ->assertSessionHasErrors('date');

        $this->assertStringContainsString(
            'YYYY-MM-DD',
            session('errors')->first('date')
        );
    }

    public function test_a_malformed_month_is_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.attendances.month', ['month' => '06/2026']))
            ->assertSessionHasErrors('month');
    }

    public function test_an_unknown_office_filter_is_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.attendances.month', ['office_id' => 999999]))
            ->assertSessionHasErrors('office_id');
    }

    /*
    |--------------------------------------------------------------------------
    | The day report
    |--------------------------------------------------------------------------
    */

    public function test_the_day_report_only_lists_the_requested_day(): void
    {
        $onDay = Attendance::factory()->create(['work_date' => '2026-10-06']);
        $otherDay = Attendance::factory()->create(['work_date' => '2026-10-05']);

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.day', ['date' => '2026-10-06']));

        $response->assertOk();

        $rows = $response->viewData('records');

        $this->assertCount(1, $rows->getCollection());
        $this->assertSame(
            $onDay->staff_id,
            $rows->getCollection()->first()->staff_id
        );
        $this->assertNotEquals($otherDay->staff_id, $rows->getCollection()->first()->staff_id);
    }

    public function test_the_day_report_filters_by_status(): void
    {
        Attendance::factory()->create(['work_date' => '2026-10-06', 'status' => Attendance::STATUS_PRESENT]);
        Attendance::factory()->late()->create(['work_date' => '2026-10-06', 'status' => Attendance::STATUS_LATE]);

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.day', [
            'date' => '2026-10-06',
            'status' => Attendance::STATUS_LATE,
        ]));

        $response->assertOk();

        $rows = $response->viewData('records');

        $this->assertCount(1, $rows->getCollection());
        $this->assertSame(
            Attendance::STATUS_LATE,
            $rows->getCollection()->first()->status
        );
    }

    public function test_the_day_report_searches_by_name_and_by_staff_code(): void
    {
        Attendance::factory()->create([
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
        ]);
        Attendance::factory()->create([
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
        ]);

        $wanted = StaffV2::query()->orderBy('id')->first();

        $this->actingAs(User::factory()->admin()->create());

        $byCode = $this->get(route('admin.attendances.day', [
            'date' => '2026-10-06',
            'q' => $wanted->staff_id,
        ]));

        $byCode->assertOk();
        $this->assertCount(1, $byCode->viewData('records')->getCollection());

        $byName = $this->get(route('admin.attendances.day', [
            'date' => '2026-10-06',
            'q' => $wanted->full_name,
        ]));

        $byName->assertOk();
        $this->assertCount(1, $byName->viewData('records')->getCollection());
    }

    public function test_the_day_summary_counts_the_whole_day_not_the_current_page(): void
    {
        Attendance::factory()->count(3)->create([
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
        ]);
        Attendance::factory()->count(2)->late()->create(['work_date' => '2026-10-06']);

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.day', ['date' => '2026-10-06']));

        $summary = $response->viewData('summary');

        $this->assertSame(5, $summary['total']);
        $this->assertSame(3, $summary['present']);
        $this->assertSame(2, $summary['late']);
        $this->assertSame(0, $summary['absent']);
    }

    public function test_the_day_report_defaults_to_today(): void
    {
        Attendance::factory()->create([
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
        ]);

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.day'));

        $response->assertOk();

        $this->assertCount(1, $response->viewData('records')->getCollection());
        $this->assertSame(
            '2026-10-06',
            $response->viewData('day')->toDateString()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | The month report
    |--------------------------------------------------------------------------
    */

    public function test_the_month_report_totals_one_persons_hours_and_days(): void
    {
        $staff = StaffV2::factory()->create();

        // Three separate days: the table allows one row per person per day.
        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-05',
            'status' => Attendance::STATUS_PRESENT,
            'worked_hours' => 8.0,
        ]);
        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
            'worked_hours' => 8.0,
        ]);
        Attendance::factory()->late()->create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-07',
            'worked_hours' => 7.5,
        ]);

        // Outside the month being reported, so it must not be counted.
        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => '2026-09-30',
            'status' => Attendance::STATUS_PRESENT,
            'worked_hours' => 8.0,
        ]);

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.month', ['month' => '2026-10']));

        $response->assertOk();

        $rows = collect($response->viewData('rows')->getCollection())
            ->firstWhere('id', $staff->id);

        $this->assertNotNull($rows, 'The person is missing from the month report.');
        $this->assertEquals(23.5, (float) $rows->hours_total);
        $this->assertSame(2, (int) $rows->present_count);
        $this->assertSame(1, (int) $rows->late_count);
        $this->assertSame(0, (int) $rows->absent_count);
    }

    public function test_the_month_report_leaves_out_people_who_never_clocked_on(): void
    {
        $clockedOn = StaffV2::factory()->create();
        StaffV2::factory()->create();

        Attendance::factory()->create([
            'staff_id' => $clockedOn->id,
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
        ]);

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.month', ['month' => '2026-10']));

        $rows = $response->viewData('rows');

        $this->assertCount(1, $rows->getCollection());
        $this->assertSame($clockedOn->id, $rows->getCollection()->first()->id);
    }

    public function test_the_month_report_filters_by_office(): void
    {
        // The factory leaves office_id null, so the two people have to be
        // placed in different offices for the filter to mean anything.
        $wanted = StaffV2::factory()->forOffice(Office::factory()->create())->create();
        $other = StaffV2::factory()->forOffice(Office::factory()->create())->create();

        foreach ([$wanted, $other] as $person) {
            Attendance::factory()->create([
                'staff_id' => $person->id,
                'work_date' => '2026-10-06',
                'status' => Attendance::STATUS_PRESENT,
            ]);
        }

        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.attendances.month', [
            'month' => '2026-10',
            'office_id' => $wanted->office_id,
        ]));

        $response->assertOk();

        $rows = $response->viewData('rows');

        $this->assertCount(1, $rows->getCollection());
        $this->assertSame($wanted->id, $rows->getCollection()->first()->id);
    }
}
