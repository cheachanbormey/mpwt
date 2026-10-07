<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'staff_v2_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * System roles. There are only two in this application.
     *
     * @var list<string>
     */
    public const ROLE_ADMIN = 'admin';

    public const ROLE_STAFF = 'staff';

    /**
     * Every role the application accepts, with its Khmer label.
     *
     * @var array<string, string>
     */
    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'អ្នកគ្រប់គ្រង',
        self::ROLE_STAFF => 'បុគ្គលិក',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The staff record linked to this login account.
     */
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    /**
     * The staff record in the rebuilt module linked to this login account.
     *
     * The key sits on this table rather than on staff_v2, so this points
     * outwards at one record instead of collecting a row.
     */
    public function staffV2(): BelongsTo
    {
        return $this->belongsTo(StaffV2::class, 'staff_v2_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    /**
     * Every role the system accepts.
     *
     * @return list<string>
     */
    public static function roles(): array
    {
        return array_keys(self::ROLE_LABELS);
    }

    /**
     * True when this account has the administrator role.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * True when this account has the staff role.
     */
    public function isStaffRole(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * The Khmer label for the account's role.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }

    /**
     * Name to show in the navbar and on screen.
     *
     * Prefers the linked staff record (Khmer name) and falls back to the
     * login account name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->staff?->name_km ?: $this->name;
    }

    /**
     * Initials used for avatar placeholders.
     */
    public function getInitialsAttribute(): string
    {
        $source = $this->staff?->name_km ?: $this->name;

        $parts = preg_split('/\s+/u', trim((string) $source), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($parts === []) {
            return mb_strtoupper(mb_substr((string) $this->email, 0, 2));
        }

        $first = mb_substr($parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';

        return mb_strtoupper($first.$last);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Accounts with the given role.
     */
    public function scopeOfRole(Builder $query, string $role): void
    {
        $query->where('role', $role);
    }

    /**
     * Administrator accounts.
     */
    public function scopeAdmins(Builder $query): void
    {
        $query->where('role', self::ROLE_ADMIN);
    }
}
