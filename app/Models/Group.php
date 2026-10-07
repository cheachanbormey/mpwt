<?php

namespace App\Models;

use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'name_en', 'description', 'leader_staff_id', 'is_active'])]
class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The member of staff leading this group.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'leader_staff_id');
    }

    /**
     * Membership rows for this group.
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Members of staff in this group.
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'group_members')
            ->withPivot(['role_in_group', 'is_leader', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Only active groups.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
