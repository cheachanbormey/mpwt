<?php

namespace Database\Seeders;

use App\Models\PositionV2;
use Illuminate\Database\Seeder;

class PositionV2Seeder extends Seeder
{
    /**
     * Seed the positions_v2 table.
     *
     * The Khmer name is the lookup key so re-running the seeder never
     * creates a duplicate position.
     */
    public function run(): void
    {
        $positions = [
            ['kh' => 'ប្រធានការដ្ឋានគ្រប់គ្រង', 'en' => 'Director General', 'description' => 'មានសមត្ថកិច្ចគ្រប់គ្រងទូទៅលើការងារទាំងអស់។'],
            ['kh' => 'អនុប្បផ្តង់ការងារ', 'en' => 'Deputy Director', 'description' => 'ជំនួយប្រធានការដ្ឋានគ្រប់គ្រង។'],
            ['kh' => 'ប្រធានផ្ទាំង', 'en' => 'Department Director', 'description' => 'ដឹកនាំផ្ទាំងមួយៗ។'],
            ['kh' => 'ប្រធានការងារ', 'en' => 'Office Chief', 'description' => 'ដឹកនាំការងារនៃផ្ទាំង។'],
            ['kh' => 'មានវិជ្ជាកម្ម', 'en' => 'Expert', 'description' => 'បុគ្គលជំនាញបច្ចេកទេសន៍ផ្នែកជួសជុល។'],
            ['kh' => 'បុគ្គលិកបច្ចេកវិជ្ជា', 'en' => 'Technical Officer', 'description' => 'បុគ្គលិកបច្ចេកវាងបរិក្ខារណ៍។'],
            ['kh' => 'បុគ្គលិកការការងារ', 'en' => 'Office Staff', 'description' => 'បុគ្គលិកប្រចាំការការងារទូទៅ។'],
            ['kh' => 'អ្នកជំនួយសារ', 'en' => 'Secretary', 'description' => 'ជំនួយការចំណូលក្នុងកិច្ចប្រជុំនិងកំណត់ត្រា។'],
            ['kh' => 'អ្នកបើក', 'en' => 'Driver', 'description' => 'ដឹកជញ្ជូនម៉ាស៊ីនសម្រាប់ការងារ។'],
            ['kh' => 'មន្ត្រីការការពារ', 'en' => 'Security Guard', 'description' => 'ការពារសុវត្ថិភាពនៅសាលាការងារ។'],
        ];

        foreach ($positions as $position) {
            PositionV2::firstOrCreate(
                ['position_name_kh' => $position['kh']],
                [
                    'position_name_en' => $position['en'],
                    'description' => $position['description'],
                    'status' => PositionV2::STATUS_ACTIVE,
                ]
            );
        }
    }
}
