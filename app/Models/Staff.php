<?php

namespace App\Models;

use Database\Factories\StaffFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id',
    'office_id',
    'position_id',
    'staff_code',
    'name_km',
    'name_en',
    'gender',
    'date_of_birth',
    'national_id',
    'phone',
    'email',
    'address',
    'photo',
    'hire_date',
    'employment_status',
    'base_salary',
])]
class Staff extends Model
{
    /** @use HasFactory<StaffFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Employment states a member of staff can be in.
     *
     * These are the canonical codes held in `employment_status`. The
     * Khmer labels live in the `employment_statuses` lookup table so an
     * administrator can rename a state without touching code.
     *
     * @var list<string>
     */
    public const STATUS_ACTIVE = 'active';

    public const STATUS_PROBATION = 'probation';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_ON_LEAVE = 'on_leave';

    public const STATUS_RETIRED = 'retired';

    public const STATUS_TERMINATED = 'terminated';

    /**
     * True when this person holds the given employment code.
     */
    public function hasEmploymentStatus(string $status): bool
    {
        return $this->employment_status === $status;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'base_salary' => 'decimal:2',
            'employment_status' => 'string',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The login account linked to this member of staff.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The office this person works in.
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * The position this person holds.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * The lookup row for this person's employment state.
     */
    public function employmentStatusRecord(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status', 'code');
    }

    /**
     * Daily attendance records.
     *
     * Attendance rows are keyed to staff_v2, so reaching them from this legacy
     * record would match on a coincidental id and quietly report another
     * person's hours. Read them from StaffV2::attendances() instead.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Leave requests submitted by this person.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Leave requests this person approved or rejected.
     */
    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by_staff_id');
    }

    /**
     * Group membership records.
     */
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Groups this person belongs to.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot(['role_in_group', 'is_leader', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Groups this person leads.
     */
    public function ledGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'leader_staff_id');
    }

    /**
     * Meetings this person created.
     */
    public function createdMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by_staff_id');
    }

    /**
     * Meeting participation records.
     */
    public function meetingParticipations(): HasMany
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    /**
     * Meetings this person was invited to.
     */
    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants')
            ->withPivot(['attendance_status', 'attended_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Meeting agenda items this person presents.
     */
    public function presentedAgendas(): HasMany
    {
        return $this->hasMany(MeetingAgenda::class, 'presenter_staff_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Currently employed staff.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('employment_status', self::STATUS_ACTIVE);
    }

    /**
     * Staff hired on or after the given date.
     */
    #[Scope]
    protected function hiredBetween(Builder $query, Carbon|string $from, Carbon|string $to): void
    {
        $query->whereBetween('hire_date', [$from, $to]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Initials used for avatar placeholders.
     */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/u', trim($this->name_km), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($parts === []) {
            return '';
        }

        $first = mb_substr($parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';

        return mb_strtoupper($first.$last);
    }

    /**
     * True when the person is currently employed.
     */
    public function isActive(): bool
    {
        return $this->employment_status === self::STATUS_ACTIVE;
    }
}
