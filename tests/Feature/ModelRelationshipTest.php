<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Location;
use App\Models\Meeting;
use App\Models\MeetingAgenda;
use App\Models\MeetingParticipant;
use App\Models\MeetingType;
use App\Models\Office;
use App\Models\Position;
use App\Models\Staff;
use App\Models\StaffV2;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Phase 02 - Eloquent relationship verification.
 */
class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Department -> Offices
    |--------------------------------------------------------------------------
    */

    public function test_department_has_many_offices(): void
    {
        $department = Department::factory()->create();
        $offices = Office::factory()->count(3)->forDepartment($department)->create();

        $this->assertCount(3, $department->offices);
        $this->assertSame(
            $offices->pluck('id')->sort()->values()->all(),
            $department->offices->pluck('id')->sort()->values()->all()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Office -> Staff
    |--------------------------------------------------------------------------
    */

    public function test_office_has_many_staff(): void
    {
        $office = Office::factory()->create();

        Staff::factory()->count(4)->create(['office_id' => $office->id]);
        Staff::factory()->create(['office_id' => null]);

        $this->assertCount(4, $office->staff);
    }

    public function test_staff_belongs_to_office(): void
    {
        $office = Office::factory()->create();
        $staff = Staff::factory()->create(['office_id' => $office->id]);

        $this->assertInstanceOf(Office::class, $staff->office);
        $this->assertTrue($office->is($staff->office));
    }

    /*
    |--------------------------------------------------------------------------
    | Position -> Staff
    |--------------------------------------------------------------------------
    */

    public function test_position_has_many_staff(): void
    {
        $position = Position::factory()->create();

        Staff::factory()->count(2)->create(['position_id' => $position->id]);

        $this->assertCount(2, $position->staff);
    }

    /*
    |--------------------------------------------------------------------------
    | Staff -> Attendances
    |--------------------------------------------------------------------------
    */

    public function test_staff_has_many_attendances(): void
    {
        $staff = StaffV2::factory()->create();

        Attendance::factory()->count(5)->create(['staff_id' => $staff->id]);

        $this->assertCount(5, $staff->attendances);
        $this->assertInstanceOf(Attendance::class, $staff->attendances->first());
    }

    public function test_attendance_belongs_to_staff(): void
    {
        $staff = StaffV2::factory()->create();
        $attendance = Attendance::factory()->create(['staff_id' => $staff->id]);

        $this->assertTrue($staff->is($attendance->staff));
    }

    /*
    |--------------------------------------------------------------------------
    | Staff -> LeaveRequests / LeaveType -> LeaveRequests
    |--------------------------------------------------------------------------
    */

    public function test_staff_has_many_leave_requests(): void
    {
        $staff = Staff::factory()->create();

        LeaveRequest::factory()->count(3)->create(['staff_id' => $staff->id]);

        $this->assertCount(3, $staff->leaveRequests);
    }

    public function test_leave_type_has_many_leave_requests(): void
    {
        $type = LeaveType::factory()->create();

        LeaveRequest::factory()->count(5)->create(['leave_type_id' => $type->id]);

        $this->assertCount(5, $type->leaveRequests);
    }

    public function test_leave_request_belongs_to_staff_and_leave_type(): void
    {
        $request = LeaveRequest::factory()->create();

        $this->assertInstanceOf(Staff::class, $request->staff);
        $this->assertInstanceOf(LeaveType::class, $request->leaveType);
    }

    public function test_leave_request_approver_is_a_separate_staff_relation(): void
    {
        $approver = Staff::factory()->create();
        $request = LeaveRequest::factory()->approved($approver)->create();

        $this->assertTrue($approver->is($request->approver));
        $this->assertCount(1, $approver->approvedLeaveRequests);
    }

    /*
    |--------------------------------------------------------------------------
    | Group -> GroupMembers, Staff -> GroupMembers
    |--------------------------------------------------------------------------
    */

    public function test_group_has_many_members_through_group_members(): void
    {
        $group = Group::factory()->create();

        // One membership row per distinct member of staff.
        foreach (Staff::factory()->count(3)->create() as $member) {
            GroupMember::factory()->forGroupAndStaff($group, $member)->create();
        }

        $this->assertCount(3, $group->members);
        $this->assertCount(3, $group->fresh()->members);
    }

    public function test_staff_belongs_to_many_groups(): void
    {
        $staff = Staff::factory()->create();
        $groupA = Group::factory()->create();
        $groupB = Group::factory()->create();

        $staff->groups()->attach($groupA->id, ['role_in_group' => 'សមាជិក', 'is_leader' => false]);
        $staff->groups()->attach($groupB->id, ['role_in_group' => 'ប្រធាន', 'is_leader' => true]);

        $this->assertCount(2, $staff->groups);
        $this->assertCount(2, $staff->fresh()->groups);

        // Pivot values live on ->pivot, not on the model itself.
        $attached = $staff->groups()->get();

        $this->assertEqualsCanonicalizing(
            ['សមាជិក', 'ប្រធាន'],
            $attached->map(fn ($group) => $group->pivot->role_in_group)->all()
        );

        $this->assertTrue((bool) $attached->firstWhere('id', $groupB->id)->pivot->is_leader);
    }

    public function test_staff_has_many_group_memberships(): void
    {
        $staff = Staff::factory()->create();
        $groups = Group::factory()->count(2)->create();

        foreach ($groups as $group) {
            $staff->groups()->attach($group->id);
        }

        $this->assertCount(2, $staff->groupMemberships);
    }

    public function test_group_has_a_leader(): void
    {
        $leader = Staff::factory()->create();
        $group = Group::factory()->create(['leader_staff_id' => $leader->id]);

        $this->assertTrue($leader->is($group->leader));
        $this->assertCount(1, $leader->ledGroups);
    }

    /*
    |--------------------------------------------------------------------------
    | MeetingType -> Meetings, Location -> Meetings
    |--------------------------------------------------------------------------
    */

    public function test_meeting_type_has_many_meetings(): void
    {
        $type = MeetingType::factory()->create();

        Meeting::factory()->count(4)->create(['meeting_type_id' => $type->id]);

        $this->assertCount(4, $type->meetings);
    }

    public function test_location_has_many_meetings(): void
    {
        $location = Location::factory()->create();

        Meeting::factory()->count(2)->create(['location_id' => $location->id]);

        $this->assertCount(2, $location->meetings);
    }

    public function test_meeting_belongs_to_type_location_and_creator(): void
    {
        $creator = Staff::factory()->create();
        $location = Location::factory()->create();
        $meeting = Meeting::factory()->create([
            'created_by_staff_id' => $creator->id,
            'location_id' => $location->id,
        ]);

        $this->assertInstanceOf(MeetingType::class, $meeting->meetingType);
        $this->assertTrue($creator->is($meeting->creator));
        $this->assertTrue($location->is($meeting->location));
    }

    /*
    |--------------------------------------------------------------------------
    | Meeting -> MeetingAgendas
    |--------------------------------------------------------------------------
    */

    public function test_meeting_has_many_agendas_ordered_by_sort_order(): void
    {
        $meeting = Meeting::factory()->create();

        MeetingAgenda::factory()->forMeeting($meeting, 3)->create(['title' => 'Third']);
        MeetingAgenda::factory()->forMeeting($meeting, 1)->create(['title' => 'First']);
        MeetingAgenda::factory()->forMeeting($meeting, 2)->create(['title' => 'Second']);

        $this->assertCount(3, $meeting->agendas);
        $this->assertSame(['First', 'Second', 'Third'], $meeting->agendas->pluck('title')->all());
    }

    public function test_agenda_numbers_auto_increment_when_not_given(): void
    {
        $meeting = Meeting::factory()->create();

        foreach (range(1, 3) as $i) {
            MeetingAgenda::factory()->forMeeting($meeting)->create(['title' => "Item {$i}"]);
        }

        $this->assertSame([1, 2, 3], $meeting->agendas->pluck('agenda_no')->all());
        $this->assertSame([1, 2, 3], $meeting->agendas->pluck('sort_order')->all());
    }

    public function test_meeting_agenda_has_a_presenter(): void
    {
        $presenter = Staff::factory()->create();
        $agenda = MeetingAgenda::factory()->create(['presenter_staff_id' => $presenter->id]);

        $this->assertTrue($presenter->is($agenda->presenter));
        $this->assertCount(1, $presenter->presentedAgendas);
    }

    /*
    |--------------------------------------------------------------------------
    | Meeting -> MeetingParticipants
    |--------------------------------------------------------------------------
    */

    public function test_meeting_has_many_participants(): void
    {
        $meeting = Meeting::factory()->create();

        MeetingParticipant::factory()->count(5)->create(['meeting_id' => $meeting->id]);

        $this->assertCount(5, $meeting->participants);
    }

    public function test_staff_belongs_to_many_meetings(): void
    {
        $staff = Staff::factory()->create();
        $meetingA = Meeting::factory()->create();
        $meetingB = Meeting::factory()->create();

        $staff->meetings()->attach($meetingA->id, ['attendance_status' => 'invited']);
        $staff->meetings()->attach($meetingB->id, ['attendance_status' => 'attended']);

        $this->assertCount(2, $staff->meetings);
        $this->assertCount(2, $staff->fresh()->meetings);
    }

    public function test_meeting_participant_belongs_to_meeting_and_staff(): void
    {
        $participant = MeetingParticipant::factory()->create();

        $this->assertInstanceOf(Meeting::class, $participant->meeting);
        $this->assertInstanceOf(Staff::class, $participant->staff);
    }

    /*
    |--------------------------------------------------------------------------
    | User -> Staff
    |--------------------------------------------------------------------------
    */

    public function test_user_has_one_staff_record(): void
    {
        $user = User::factory()->create();
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Staff::class, $user->staff);
        $this->assertTrue($staff->is($user->staff));
        $this->assertTrue($user->is($staff->user));
    }

    public function test_staff_user_id_is_optional(): void
    {
        $staff = Staff::factory()->create(['user_id' => null]);

        $this->assertNull($staff->user);
    }

    /*
    |--------------------------------------------------------------------------
    | Soft deletes & casts
    |--------------------------------------------------------------------------
    */

    public function test_staff_uses_soft_deletes(): void
    {
        $staff = Staff::factory()->create();

        $staff->delete();

        $this->assertSoftDeleted($staff);
        $this->assertNull(Staff::find($staff->id));
        $this->assertTrue($staff->is(Staff::withTrashed()->find($staff->id)));
        $this->assertTrue($staff->is(Staff::onlyTrashed()->find($staff->id)));
    }

    public function test_staff_casts_dates_and_salary(): void
    {
        $staff = Staff::factory()->create([
            'hire_date' => '2024-01-15',
            'base_salary' => '1500.50',
        ]);

        $this->assertInstanceOf(Carbon::class, $staff->hire_date);
        $this->assertSame('2024-01-15', $staff->hire_date->toDateString());
        $this->assertSame('1500.50', $staff->base_salary);
    }
}
