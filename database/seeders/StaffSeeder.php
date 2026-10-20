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
        $offices = Office::pluck('id', 'office_code');
        $positions = Position::pluck('id');

        $rows = [
            ['staff_id' => 'ST-00001', 'name_kh' => 'ពេជ្រ សុធារ៉ា', 'name_en' => 'Pech Sotheara', 'office_code' => 'OFF-003', 'position_id' => '1', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00002', 'name_kh' => 'ហួន សុខវណ្ណ', 'name_en' => 'Houn Sokhavana', 'office_code' => 'OFF-001', 'position_id' => '2', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00003', 'name_kh' => 'លាង សុភាព', 'name_en' => 'Leang Sopheaផ', 'office_code' => 'OFF-001', 'position_id' => '3', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00004', 'name_kh' => 'សុខ សុវណ្ណ', 'name_en' => 'Sok Sovann', 'office_code' => 'OFF-002', 'position_id' => '5', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00005', 'name_kh' => 'សុភា សៀមណាន', 'name_en' => 'Sopheak Seaman', 'office_code' => 'OFF-002', 'position_id' => '6', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00006', 'name_kh' => 'កុយ សុភាលីន', 'name_en' => 'Kry Soph_idealyn', 'office_code' => 'OFF-003', 'position_id' => '7', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00007', 'name_kh' => 'ប៊ុន សុធាយ', 'name_en' => 'Born Sotheary', 'office_code' => 'OFF-004', 'position_id' => '7', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00008', 'name_kh' => 'រាជា វង្ស', 'name_en' => 'Rachea Vong', 'office_code' => 'OFF-005', 'position_id' => '8', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00009', 'name_kh' => 'សុភា ណីន', 'name_en' => 'Sopheak Neen', 'office_code' => 'OFF-005', 'position_id' => '6', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00010', 'name_kh' => 'ហាកុង រុណ', 'name_en' => 'Hakorn Roun', 'office_code' => 'OFF-006', 'position_id' => '7', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00011', 'name_kh' => 'ទាវុស ប៊ិល', 'name_en' => 'Tavuth Ben', 'office_code' => 'OFF-006', 'position_id' => '8', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00012', 'name_kh' => 'សៀម ឈួន', 'name_en' => 'Seth Chhun', 'office_code' => 'OFF-007', 'position_id' => '4', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00013', 'name_kh' => 'នាតា សៀវ', 'name_en' => 'Nadasy', 'office_code' => 'OFF-007', 'position_id' => '8', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00014', 'name_kh' => 'មោត់ សៀវិន', 'name_en' => 'Moat Sophean', 'office_code' => 'OFF-008', 'position_id' => '7', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00015', 'name_kh' => 'សុខ ឈួន', 'name_en' => 'Sok Chhun', 'office_code' => 'OFF-008', 'position_id' => '9', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00016', 'name_kh' => 'រីនា ប៊ីន', 'name_en' => 'Rinea Ben', 'office_code' => 'OFF-009', 'position_id' => '7', 'gender' => 'female', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00017', 'name_kh' => 'វិសុន ចាន់', 'name_en' => 'Visun Chan', 'office_code' => 'OFF-009', 'position_id' => '9', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
            ['staff_id' => 'ST-00018', 'name_kh' => 'រុន សុភា', 'name_en' => 'Roun Sopheak', 'office_code' => 'OFF-010', 'position_id' => '6', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],                                                            
            ['staff_id' => 'ST-00020', 'name_kh' => 'ចន្រ ពោធិ', 'name_en' => 'Chhorn Poth', 'office_code' => 'OFF-001', 'position_id' => '10', 'gender' => 'male', 'status' => Staff::STATUS_ACTIVE],
        ];

        foreach ($rows as $index => $row) {
            if (Staff::where('staff_id', $row['staff_id'])->exists()) {
                continue;
            }

            // The first three accounts get a login user attached.
            $userId = null;

            if ($index < 3) {
                $user = User::firstOrCreate(
                    ['email' => str($row['name_en'])->slug()->value().'@mpwt.gov.kh'],
                    [
                        'name' => $row['name_kh'],
                        'password' => Hash::make('password'),
                    ]
                );

                $user->forceFill(['email_verified_at' => now()])->save();
                $userId = $user->id;
            }

            Staff::create([
                'user_id' => $userId,
                'office_code' => $offices[$row['office_code']] ?? null,
                'position_id' => $positions[$row['position_id']] ?? null,
                'staff_id' => $row['staff_id'],
                'name_km' => $row['name_kh'],
                'name_en' => $row['name_en'],
                'gender' => $row['gender'],
                'phone' => fake()->numerify('01########'),
                'email' => str($row['name_en'])->slug()->value().'@mpwt.gov.kh',
                // 'telegram' => str($row['name_en'])->slug()->value().'@telegram.me',
                'telegram' => 'https://t.me' . str($row['name_en'])->slug()->value(),
                'status' => $row['status'],
                'system_role' => Staff::ROLE_STAFF,

            ]);
        }
    }
}
