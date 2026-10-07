<?php

namespace App\Models;

use Database\Factories\LeaveRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'staff_id',
    'leave_type_id',
    'start_date',
    'end_date',
    'total_days',
    'reason',
    'status',
    'approved_by_staff_id',
    'approved_at',
    'rejection_reason',
])]
class LeaveRequest extends Model
{
    /** @use HasFactory<LeaveRequestFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * The member of staff who requested the leave.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * The type of leave requested.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * The member of staff who approved or rejected the request.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by_staff_id');
    }

    /**
     * Requests still waiting for a decision.
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Requests that were approved.
     */
    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Requests that overlap the given date.
     */
    #[Scope]
    protected function overlapping(Builder $query, $from, $to): void
    {
        $query->whereDate('start_date', '<=', $to)
            ->whereDate('end_date', '>=', $from);
    }
}
