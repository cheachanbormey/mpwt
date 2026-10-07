<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Accounts used to try the two roles from the login screen.
     *
     * The emails match the buttons on resources/views/auth/login.blade.php.
     */
    public const ADMIN_EMAIL = 'admin@mpwt.gov.kh';

    public const STAFF_EMAIL = 'sokha@mpwt.gov.kh';

    public const DEMO_PASSWORD = 'password';

    /**
     * Assign roles.
     *
     * The users table defaults to 'staff', so this only has to create the
     * admin account and guarantee nobody is left without a role. Both demo
     * accounts are linked to an existing staff row so the self-service pages
     * work for an administrator too.
     */
    public function run(): void
    {
        $admin = $this->account(
            self::ADMIN_EMAIL,
            'អ្នកគ្រប់គ្រង',
            User::ROLE_ADMIN
        );

        $staffUser = $this->account(
            self::STAFF_EMAIL,
            'បុគ្គលិក',
            User::ROLE_STAFF
        );

        // Everyone else is an ordinary staff account.
        User::query()
            ->whereNotIn('id', [$admin->id, $staffUser->id])
            ->where(function ($query) {
                $query->whereNull('role')
                    ->orWhereNotIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF]);
            })
            ->update(['role' => User::ROLE_STAFF]);

        // Link each demo login to a staff record that has no account yet.
        $this->attachStaff($admin);
        $this->attachStaff($staffUser);
    }

    /**
     * Create or refresh one demo account.
     */
    private function account(string $email, string $name, string $role): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->name = $name;
        $user->role = $role;

        // User casts `password` to 'hashed', so the plain value is enough.
        $user->password = self::DEMO_PASSWORD;
        $user->email_verified_at = now();

        $user->save();

        return $user;
    }

    /**
     * Point an account at the first staff row that has no login yet.
     *
     * Deterministic: StaffSeeder always leaves the same rows unlinked, so
     * repeated seeding reuses them instead of creating new staff.
     */
    private function attachStaff(User $user): void
    {
        if (Staff::where('user_id', $user->id)->exists()) {
            return;
        }

        $staff = Staff::query()
            ->whereNull('user_id')
            ->orderBy('staff_code')
            ->first();

        if ($staff === null) {
            return;
        }

        $staff->forceFill(['user_id' => $user->id])->save();
    }
}
