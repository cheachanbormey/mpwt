<?php

namespace App\Models;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'staff_id',
    'work_date',
    'check_in',
    'check_out',
    'worked_hours',
    'status',
    'check_in_source',
    'notes',
])]
class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const STATUS_PRESENT = 'present';

    public const STATUS_ABSENT = 'absent';

    public const STATUS_LATE = 'late';

    public const STATUS_LEAVE = 'leave';

    public const STATUS_HOLIDAY = 'holiday';

    public const STATUS_WEEKEND = 'weekend';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
            'worked_hours' => 'decimal:2',
        ];
    }

    /**
     * The member of staff this record belongs to.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffV2::class);
    }

    /**
     * Records for one specific day.
     */
    #[Scope]
    protected function onDate(Builder $query, $date): void
    {
        $query->whereDate('work_date', $date);
    }

    /**
     * Records inside a date range.
     */
    #[Scope]
    protected function betweenDates(Builder $query, $from, $to): void
    {
        $query->whereBetween('work_date', [$from, $to]);
    }

    /**
     * Records from the start of the given month to its end.
     */
    #[Scope]
    protected function inMonth(Builder $query, string $month): void
    {
        $start = Carbon::parse($month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query->whereBetween('work_date', [$start->toDateString(), $end->toDateString()]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * True once the person has arrived but has not left yet.
     */
    public function isOpen(): bool
    {
        return $this->check_in !== null && $this->check_out === null;
    }

    /**
     * True once the person has arrived and left.
     */
    public function isClosed(): bool
    {
        return $this->check_in !== null && $this->check_out !== null;
    }

    /**
     * Khmer labels for the status column.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_PRESENT => 'វត្តមាន',
            self::STATUS_ABSENT => 'អវត្តមាន',
            self::STATUS_LATE => 'យឺត',
            self::STATUS_LEAVE => 'សុំច្បាប់',
            self::STATUS_HOLIDAY => 'បុណ្យ',
            self::STATUS_WEEKEND => 'ចុចអាទិត្យ',
        ];
    }
}
