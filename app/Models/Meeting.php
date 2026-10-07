<?php

namespace App\Models;

use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'meeting_type_id',
    'location_id',
    'created_by_staff_id',
    'title',
    'agenda_summary',
    'meeting_date',
    'start_time',
    'end_time',
    'status',
])]
class Meeting extends Model
{
    /** @use HasFactory<MeetingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_ONGOING = 'ongoing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
        ];
    }

    /**
     * The type of this meeting.
     */
    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    /**
     * Where the meeting takes place.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * The member of staff who organised this meeting.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }

    /**
     * Agenda items, already ordered.
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(MeetingAgenda::class)->orderBy('sort_order');
    }

    /**
     * Participation rows for this meeting.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    /**
     * Members of staff invited to this meeting.
     */
    public function staffParticipants(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'meeting_participants')
            ->withPivot(['attendance_status', 'attended_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Meetings that have not been cancelled.
     */
    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->whereDate('meeting_date', '>=', today())
            ->where('status', self::STATUS_SCHEDULED);
    }
}
