<?php

namespace App\Models;

use Database\Factories\PositionV2Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['position_name_kh', 'position_name_en', 'description', 'status'])]
class PositionV2 extends Model
{
    /** @use HasFactory<PositionV2Factory> */
    use HasFactory;

    /**
     * Positions a person can be shown as holding.
     */
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'positions_v2';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Staff members holding this position.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(StaffV2::class, 'position_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Only active positions.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Only inactive positions.
     */
    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', self::STATUS_INACTIVE);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * True when the position is offered to staff.
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

    /**
     * The label to show for this position, preferring the Khmer name.
     */
    public function displayName(): string
    {
        return $this->position_name_kh !== ''
            ? $this->position_name_kh
            : (string) $this->position_name_en;
    }
}
