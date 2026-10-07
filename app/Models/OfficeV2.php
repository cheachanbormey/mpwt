<?php

namespace App\Models;

use Database\Factories\OfficeV2Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An office that sits under a department.
 */
#[Fillable(['dep_id', 'office_code', 'office_name', 'location', 'description', 'status'])]
class OfficeV2 extends Model
{
    /** @use HasFactory<OfficeV2Factory> */
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
    protected $table = 'offices_v2';

    /**
     * The department this office belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(DepartmentV2::class, 'dep_id');
    }

    /*
     * NOTE: there is deliberately no staff() relation here. The staff table
     * points its office_id at the original offices table, so claiming staff
     * here would report rows that have nothing to do with offices_v2.
     */

    /**
     * Only active offices.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Only inactive offices.
     */
    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Whether the office may be used on new records.
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
