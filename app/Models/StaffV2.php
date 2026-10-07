<?php

namespace App\Models;

use Database\Factories\StaffV2Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'staff_id',
    'full_name',
    'gender',
    'position_id',
    'office_id',
    'email',
    'phone',
    'telegram',
    'status',
    'system_role',
])]
class StaffV2 extends Model
{
    /** @use HasFactory<StaffV2Factory> */
    use HasFactory;

    /**
     * Whether this person is currently working.
     */
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /**
     * Access level granted inside the system.
     *
     * These mirror the codes on `users.role` so a person can be granted
     * the same privilege here as on their login account.
     */
    public const ROLE_ADMIN = 'admin';

    public const ROLE_STAFF = 'staff';

    public const ROLE_USER = 'user';

    protected $table = 'staff_v2';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
            'gender' => 'string',
            'system_role' => 'string',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The position this person holds.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(PositionV2::class, 'position_id');
    }

    /**
     * The office this person works in.
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Daily attendance records for this person.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'staff_id');
    }

    /**
     * Leave requests submitted by this person.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'staff_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Only currently working staff.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Only staff who are not currently working.
     */
    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Staff attached to the given office.
     */
    #[Scope]
    protected function forOffice(Builder $query, int $officeId): void
    {
        $query->where('office_id', $officeId);
    }

    /**
     * Staff holding the given position.
     */
    #[Scope]
    protected function forPosition(Builder $query, int $positionId): void
    {
        $query->where('position_id', $positionId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * True when the person is currently working.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * True when this person administers the system.
     */
    public function isAdmin(): bool
    {
        return $this->system_role === self::ROLE_ADMIN;
    }

    /**
     * Khmer labels for the status column.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'ការងារសកម្ម',
            self::STATUS_INACTIVE => 'ឈប់ការងារ',
        ];
    }

    /**
     * Khmer labels for the gender column.
     *
     * @return array<string, string>
     */
    public static function genderLabels(): array
    {
        return [
            'male' => 'បុគ្គលិកបុគ្គលប្រុស',
            'female' => 'បុគ្គលិកស្រី',
            'other' => 'ផ្សេងៗ',
        ];
    }

    /**
     * Khmer labels for the system role column.
     *
     * @return array<string, string>
     */
    public static function systemRoleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'អ្នកគ្រប់គ្រង',
            self::ROLE_STAFF => 'បុគ្គលិក',
            self::ROLE_USER => 'អ្នកប្រើប្រព័ន្ធ',
        ];
    }

    /**
     * Initials used for avatar placeholders.
     */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/u', trim((string) $this->full_name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($parts === []) {
            return '';
        }

        $first = mb_substr($parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';

        return mb_strtoupper($first.$last);
    }

    /**
     * The name shown in menus and tables.
     */
    public function displayName(): string
    {
        return (string) $this->full_name;
    }
}
