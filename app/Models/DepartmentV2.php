<?php

namespace App\Models;

use Database\Factories\DepartmentV2Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A department of the organisation.
 *
 * `subordinate_offices` is deliberately NOT a column. It is the offices()
 * relation below, so the list can never drift out of step with offices_v2.
 */
#[Fillable(['dep_code', 'dep_kh_name', 'dep_en_name', 'director_dep', 'status'])]
class DepartmentV2 extends Model
{
    /** @use HasFactory<DepartmentV2Factory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /**
     * The table this model reads and writes.
     */
    protected $table = 'departments_v2';

    /**
     * Offices that report to this department.
     */
    public function offices(): HasMany
    {
        return $this->hasMany(OfficeV2::class, 'dep_id');
    }

    /**
     * The staff member heading this department.
     */
    public function director(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'director_dep');
    }

    /**
     * Only active departments.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Only inactive departments.
     */
    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Whether the department may be used on new records.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Khmer labels for the status column.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'សកម្ម',
            self::STATUS_INACTIVE => 'អសកម្ម',
        ];
    }
}
