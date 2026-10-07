<?php

namespace Tests\Feature\Attendance;

use App\Exceptions\AttendanceClockException;
use App\Models\Attendance;
use App\Models\StaffV2;
use App\Models\User;
use App\Services\AttendanceClock;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The check-in and check-out clock.
 *
 * These tests drive the service directly wherever the rule is about the
 * timestamps, because a request cannot dictate the server's clock. The HTTP
 * tests then confirm the buttons are wired to the same rules.
 */
class ClockTest extends TestCase
{
    use RefreshDatabase;

    private function clock(): AttendanceClock
    {
        return app(AttendanceClock::class);
    }

    /**
     * A signed-in account whose person is in the rebuilt staff module.
     *
     * @return array{0: User, 1: StaffV2}
     */
    private function accountWithStaff(string $role = User::ROLE_STAFF): array
    {
        $staff = StaffV2::factory()->create();
        $user = User::factory()->create(['role' => $role, 'staff_v2_id' => $staff->id]);

        return [$user, $staff->fresh()];
    }

    /*
    |--------------------------------------------------------------------------
    | Check-in
    |--------------------------------------------------------------------------
    */

    public function test_staff_can_check_in_once(): void
    {
        $staff = StaffV2::factory()->create();

        $record = $this->clock()->checkIn($staff, Carbon::parse('2026-10-06 07:30'));

        $this->assertDatabaseHas('attendances', [
            'staff_id' => $staff->id,
            'status' => Attendance::STATUS_PRESENT,
        ]);

        $this->assertSame('2026-10-06', $record->work_date->toDateString());
        $this->assertSame('07:30', $record->check_in->format('H:i'));
        $this->assertNull($record->check_out);
        $this->assertNull($record->worked_hours);
    }

    public function test_check_in_stores_today_and_the_current_time(): void
    {
        $staff = StaffV2::factory()->create();

        $this->travelTo(Carbon::parse('2026-10-06 07:05'));

        $record = $this->clock()->checkIn($staff);

        $this->assertSame('2026-10-06', $record->work_date->toDateString());
        $this->assertSame('07:05', $record->check_in->format('H:i'));
    }

    public function test_a_second_check_in_on_the_same_day_is_refused(): void
    {
        $staff = StaffV2::factory()->create();
        $clock = $this->clock();

        $clock->checkIn($staff, Carbon::parse('2026-10-06 07:30'));

        try {
            $clock->checkIn($staff, Carbon::parse('2026-10-06 09:15'));

            $this->fail('A second check-in on the same day should not be allowed.');
        } catch (AttendanceClockException $exception) {
            $this->assertStringContainsString('តែមួយដងក្នុងមួយថ្ងៃ', $exception->getMessage());
        }

        // The first record is untouched, so nobody loses their arrival time.
        $this->assertSame(1, Attendance::where('staff_id', $staff->id)->count());
        $this->assertSame(
            '07:30',
            Attendance::where('staff_id', $staff->id)->value('check_in')->format('H:i')
        );
    }

    public function test_a_duplicate_check_in_is_blocked_by_the_database_too(): void
    {
        $staff = StaffV2::factory()->create();

        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-06',
        ]);

        $this->expectException(QueryException::class);

        // The service check is a courtesy; the unique index is the real guard.
        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-06',
            'status' => Attendance::STATUS_PRESENT,
        ]);
    }

    public function test_arriving_after_the_cut_off_is_recorded_as_late(): void
    {
        $staff = StaffV2::factory()->create();

        $onTime = $this->clock()->checkIn($staff, Carbon::parse('2026-10-06 07:59'));
        $this->assertSame(Attendance::STATUS_PRESENT, $onTime->status);

        $late = $this->clock()->checkIn($staff, Carbon::parse('2026-10-07 08:01'));
        $this->assertSame(Attendance::STATUS_LATE, $late->status);
    }

    /*
    |--------------------------------------------------------------------------
    | Check-out and work hours
    |--------------------------------------------------------------------------
    */

    public function test_worked_hours_are_derived_from_the_two_timestamps(): void
    {
        $staff = StaffV2::factory()->create();
        $clock = $this->clock();

        $clock->checkIn($staff, Carbon::parse('2026-10-06 08:00'));

        $record = $clock->checkOut($staff, Carbon::parse('2026-10-06 17:30'));

        $this->assertSame('17:30', $record->check_out->format('H:i'));
        $this->assertEquals(9.5, (float) $record->worked_hours);
    }

    public function test_worked_hours_are_rounded_to_two_decimals(): void
    {
        $staff = StaffV2::factory()->create();
        $clock = $this->clock();

        $clock->checkIn($staff, Carbon::parse('2026-10-06 08:00'));
        $record = $clock->checkOut($staff, Carbon::parse('2026-10-06 16:20'));

        $this->assertEquals(8.33, (float) $record->worked_hours);
    }

    public function test_worked_hours_cannot_be_typed_in(): void
    {
        [$user, $staff] = $this->accountWithStaff();

        $this->travelTo(Carbon::parse('2026-10-06 08:00'));

        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-06',
            'check_in' => now(),
            'check_out' => null,
            'worked_hours' => null,
        ]);

        $this->travelTo(Carbon::parse('2026-10-06 17:00'));

        // A hand written total, posted alongside the button press.
        $this->actingAs($user)->post(route('staff.attendance.check-out'), [
            'worked_hours' => 99,
            'check_in' => '2020-01-01 00:00:00',
            'check_out' => '2020-01-01 23:59:00',
        ]);

        $record = Attendance::where('staff_id', $staff->id)->first();

        $this->assertEquals(9.0, (float) $record->worked_hours);
    }

    /*
    |--------------------------------------------------------------------------
    | Invalid check-out
    |--------------------------------------------------------------------------
    */

    public function test_checking_out_without_checking_in_is_refused(): void
    {
        $staff = StaffV2::factory()->create();

        $this->expectException(AttendanceClockException::class);
        $this->expectExceptionMessage('មិនទាន់មានកំណត់ត្រាចូលការងារ');

        $this->clock()->checkOut($staff, Carbon::parse('2026-10-06 17:00'));
    }

    public function test_checking_out_before_checking_in_is_refused(): void
    {
        $staff = StaffV2::factory()->create();
        $clock = $this->clock();

        $clock->checkIn($staff, Carbon::parse('2026-10-06 08:00'));

        try {
            $clock->checkOut($staff, Carbon::parse('2026-10-06 07:30'));

            $this->fail('Leaving before arriving should not be allowed.');
        } catch (AttendanceClockException $exception) {
            $this->assertStringContainsString('មិនអាចចេញការងារមុនម៉ោងចូល', $exception->getMessage());
        }

        $this->assertNull(
            Attendance::where('staff_id', $staff->id)->value('check_out'),
            'A refused check-out must leave the record as it was.'
        );
    }

    public function test_checking_out_at_the_same_instant_as_check_in_is_refused(): void
    {
        $staff = StaffV2::factory()->create();
        $clock = $this->clock();

        $clock->checkIn($staff, Carbon::parse('2026-10-06 08:00'));

        $this->expectException(AttendanceClockException::class);

        // Zero hours worked is not a shift.
        $clock->checkOut($staff, Carbon::parse('2026-10-06 08:00'));
    }

    public function test_a_second_check_out_on_the_same_day_is_refused(): void
    {
        $staff = StaffV2::factory()->create();
        $clock = $this->clock();

        $clock->checkIn($staff, Carbon::parse('2026-10-06 08:00'));
        $clock->checkOut($staff, Carbon::parse('2026-10-06 17:00'));

        $this->expectException(AttendanceClockException::class);

        $clock->checkOut($staff, Carbon::parse('2026-10-06 18:00'));
    }

    /*
    |--------------------------------------------------------------------------
    | The buttons on the screen
    |--------------------------------------------------------------------------
    */

    public function test_the_check_in_button_records_the_arrival(): void
    {
        [$user, $staff] = $this->accountWithStaff();

        $this->travelTo(Carbon::parse('2026-10-06 07:45'));

        $response = $this->actingAs($user)->post(route('staff.attendance.check-in'));

        $response->assertSessionHas('success');

        $record = Attendance::where('staff_id', $staff->id)->first();

        $this->assertSame('2026-10-06', $record->work_date->toDateString());
        $this->assertSame('07:45', $record->check_in->format('H:i'));
        $this->assertSame('system', $record->check_in_source);
    }

    public function test_pressing_check_in_twice_is_reported_rather_than_stored(): void
    {
        [$user] = $this->accountWithStaff();

        $this->actingAs($user)->post(route('staff.attendance.check-in'));

        $response = $this->actingAs($user)->post(route('staff.attendance.check-in'));

        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');
    }

    public function test_the_check_out_button_closes_the_day(): void
    {
        [$user, $staff] = $this->accountWithStaff();

        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => now()->toDateString(),
            'check_in' => now()->subHours(8),
            'check_out' => null,
            'worked_hours' => null,
        ]);

        $this->travelTo(Carbon::parse('2026-10-06 17:00'));

        $response = $this->actingAs($user)->post(route('staff.attendance.check-out'));

        $response->assertSessionHas('success');

        $record = Attendance::where('staff_id', $staff->id)->first();

        $this->assertNotNull($record->check_out);
        $this->assertNotNull($record->worked_hours);
    }

    public function test_an_invalid_check_out_is_reported_rather_than_stored(): void
    {
        [$user, $staff] = $this->accountWithStaff();

        Attendance::factory()->create([
            'staff_id' => $staff->id,
            'work_date' => now()->toDateString(),
            'check_in' => now()->subHour(),
            'check_out' => null,
            'worked_hours' => null,
        ]);

        $this->travelTo(Carbon::parse('2026-10-06 07:00'));

        $response = $this->actingAs($user)->post(route('staff.attendance.check-out'));

        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');

        $this->assertNull(Attendance::where('staff_id', $staff->id)->value('check_out'));
    }

    public function test_a_signed_out_visitor_cannot_use_the_clock(): void
    {
        $this->post(route('staff.attendance.check-in'))->assertRedirect(route('login'));

        $this->post(route('staff.attendance.check-out'))->assertRedirect(route('login'));

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_an_account_without_a_staff_record_cannot_use_the_clock(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($user)->post(route('staff.attendance.check-in'))->assertForbidden();
    }

    public function test_the_clock_buttons_cannot_be_reached_by_get(): void
    {
        $this->get(route('staff.attendance.check-in'))->assertStatus(405);

        $this->get(route('staff.attendance.check-out'))->assertStatus(405);
    }

    public function test_a_signed_in_person_only_sees_their_own_attendance(): void
    {
        [$user, $staff] = $this->accountWithStaff();
        $colleague = StaffV2::factory()->create();

        Attendance::factory()->create(['staff_id' => $staff->id]);
        Attendance::factory()->create(['staff_id' => $colleague->id]);

        $response = $this->actingAs($user)->get(route('staff.attendance'));

        $response->assertOk();
        $response->assertSee($staff->staff_id);
        $response->assertDontSee($colleague->staff_id);
    }
}
