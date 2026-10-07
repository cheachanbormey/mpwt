<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'meeting_id',
    'staff_id',
    'attendance_status',
    'attended_at',
    'notes',
])]
class MeetingParticipant extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    public const STATUS_INVITED = 'invited';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_ATTENDED = 'attended';

    public const STATUS_ABSENT = 'absent';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
        ];
    }

    /**
     * The meeting this participation belongs to.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * The invited member of staff.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
