<?php

namespace App\Models;

use Database\Factories\EmploymentStatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Staff employment states.
 *
 * Stored in the `employment_statuses` lookup table so the HR team can
 * rename a state without a migration. `sort_order` drives the display
 * order and `is_active` stops a state being removed while in use.
 */
#[Fillable(['code', 'name', 'sort_order', 'is_active'])]
class EmploymentStatus extends Model
{
    /** @use HasFactory<EmploymentStatusFactory> */
    use HasFactory;

    /**
     * Cached list so a page that renders the same states many times only
     * queries once.
     *
     * @var Collection<string, self>|null
     */
    private static ?Collection $cached = null;

    /**
     * Forget the cached list. Call after writing.
     */
    public static function flushCache(): void
    {
        self::$cached = null;
    }

    /**
     * Every state, keyed by code and ordered for display.
     *
     * @return Collection<string, self>
     */
    public static function options(): Collection
    {
        return self::$cached ??= self::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->keyBy('code');
    }

    /**
     * Khmer labels keyed by code, handy for badges.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return self::options()->mapWithKeys(
            fn (self $status) => [$status->code => $status->name]
        )->all();
    }

    /**
     * States that can still be chosen on a form.
     */
    #[Scope]
    protected function selectable(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Staff currently holding this state.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'employment_status');
    }
}
