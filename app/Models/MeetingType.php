<?php

namespace App\Models;

use Database\Factories\MeetingTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'name_en',
    'description',
    'default_duration_minutes',
    'is_active',
])]
class MeetingType extends Model
{
    /** @use HasFactory<MeetingTypeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Meetings of this type.
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Only active meeting types.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
