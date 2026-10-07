<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Position;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Seed the staff table.
     *
     * A few accounts receive a linked login user so the
     * users <-> staff relationship can be verified.
     */
    public function run(): void
    {
        $offices = Office::pluck('id', 'code');
        $positions = Position::pluck('id', 'code');

        $rows = [
            ['code' => 'ST-00001', 'name' => 'ពាយាន សុខាន់', 'en' => 'Pech Sotheara', 'office' => 'OFF-003', 'position' => 'POS-001', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00002', 'name' => 'ព្រះរាជាណាចក្រកម្ពុជា ហួន សុខវណ្ណ', 'en' => 'Houn Sokhavana', 'office' => 'OFF-001', 'position' => 'POS-002', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00003', 'name' => 'លាញ់ សុភា', 'en' => 'Leang Sopheak', 'office' => 'OFF-001', 'position' => 'POS-003', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00004', 'name' => 'ច្រាប់ មានគ្រួសារ', 'en' => 'Chrab Menchour', 'office' => 'OFF-002', 'position' => 'POS-005', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00005', 'name' => 'សុភា សៀមណាន', 'en' => 'Sopheak Seaman', 'office' => 'OFF-002', 'position' => 'POS-006', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00006', 'name' => 'កុយ សុភាលីន', 'en' => 'Kry Sophealyn', 'office' => 'OFF-003', 'position' => 'POS-007', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00007', 'name' => 'ប៊ុន សុធាយ', 'en' => 'Born Sotheary', 'office' => 'OFF-004', 'position' => 'POS-007', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00008', 'name' => 'រាជា វង្ស', 'en' => 'Rachea Vong', 'office' => 'OFF-005', 'position' => 'POS-008', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00009', 'name' => 'សុភា ណីន', 'en' => 'Sopheak Neen', 'office' => 'OFF-005', 'position' => 'POS-006', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00010', 'name' => 'ហាកុង រុណ', 'en' => 'Hakorn Roun', 'office' => 'OFF-006', 'position' => 'POS-007', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00011', 'name' => 'ទាវុស ប៊ិល', 'en' => 'Tavuth Ben', 'office' => 'OFF-006', 'position' => 'POS-008', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00012', 'name' => 'សៀម ឈួន', 'en' => 'Seth Chhun', 'office' => 'OFF-007', 'position' => 'POS-004', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00013', 'name' => 'នាតា សៀវ', 'en' => 'Nadasy', 'office' => 'OFF-007', 'position' => 'POS-008', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00014', 'name' => 'មោត់ សៀវិន', 'en' => 'Moat Sophean', 'office' => 'OFF-008', 'position' => 'POS-007', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00015', 'name' => 'សុខ ឈួន', 'en' => 'Sok Chhun', 'office' => 'OFF-008', 'position' => 'POS-009', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00016', 'name' => 'រីនា ប៊ីន', 'en' => 'Rinea Ben', 'office' => 'OFF-009', 'position' => 'POS-007', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00017', 'name' => 'វិសុន ចាន់', 'en' => 'Visun Chan', 'office' => 'OFF-009', 'position' => 'POS-009', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00018', 'name' => 'រុន សុភា', 'en' => 'Roun Sopheak', 'office' => 'OFF-010', 'position' => 'POS-006', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['code' => 'ST-00019', 'name' => 'កៀន សុវណ្ណ', 'en' => 'Kein Sovann', 'office' => 'OFF-010', 'position' => 'POS-008', 'gender' => 'male', 'status' => Staff::STATUS_PROBATION],
            ['code' => 'ST-00020', 'name' => 'ចន្រ ពោធិ', 'en' => 'Chhorn Poth', 'office' => 'OFF-001', 'position' => 'POS-010', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
        ];

        foreach ($rows as $index => $row) {
            if (Staff::where('staff_code', $row['code'])->exists()) {
                continue;
            }

            // The first three accounts get a login user attached.
            $userId = null;

            if ($index < 3) {
                $user = User::firstOrCreate(
                    ['email' => str($row['en'])->slug()->value().'@mpwt.gov.kh'],
                    [
                        'name' => $row['name'],
                        'password' => Hash::make('password'),
                    ]
                );

                $user->forceFill(['email_verified_at' => now()])->save();
                $userId = $user->id;
            }

            Staff::create([
                'user_id' => $userId,
                'office_id' => $offices[$row['office']] ?? null,
                'position_id' => $positions[$row['position']] ?? null,
                'staff_code' => $row['code'],
                'name_km' => $row['name'],
                'name_en' => $row['en'],
                'gender' => $row['gender'],
                'date_of_birth' => fake()->dateTimeBetween('-58 years', '-24 years'),
                'national_id' => fake()->unique()->numerify('#########'),
                'phone' => fake()->numerify('01########'),
                'email' => str($row['en'])->slug()->value().'@mpwt.gov.kh',
                'address' => 'រាជធានីភ្នំពេញ',
                'hire_date' => fake()->dateTimeBetween('-9 years', '-2 months'),
                'employment_status' => $row['status'],
                'base_salary' => fake()->randomFloat(2, 400, 3000),
            ]);
        }
    }
}
