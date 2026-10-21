<?php

namespace App\Models;

use Database\Factories\MeetingAgendaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'meeting_id',
    'agenda_no',
    'topic',
    'presenter_id',
    // 'description',
    // 'duration_minutes',
    // 'sort_order',
])]
class MeetingAgenda extends Model
{
    /** @use HasFactory<MeetingAgendaFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'agenda_no' => 'integer',
            // 'duration_minutes' => 'integer',
            // 'sort_order' => 'integer',
        ];
    }

    /**
     * The meeting this agenda item belongs to.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * The member of staff presenting this item.
     */
    public function presenter(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'presenter_staff_id');
    }
}
