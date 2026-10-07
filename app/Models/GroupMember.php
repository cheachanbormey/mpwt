<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['group_id', 'staff_id', 'role_in_group', 'is_leader', 'joined_at'])]
class GroupMember extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_leader' => 'boolean',
            'joined_at' => 'date',
        ];
    }

    /**
     * The group this membership belongs to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * The member of staff who joined.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
