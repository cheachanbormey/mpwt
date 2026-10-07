<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'office_id' => null,
            'position_id' => null,
            'staff_code' => 'ST-'.fake()->unique()->numerify('#####'),
            'name_km' => fake()->name('kh_KH'),
            'name_en' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-58 years', '-22 years'),
            'national_id' => fake()->unique()->numerify('#########'),
            'phone' => fake()->numerify('0########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'photo' => null,
            'hire_date' => fake()->dateTimeBetween('-10 years', '-1 month'),
            'employment_status' => Staff::STATUS_ACTIVE,
            'base_salary' => fake()->randomFloat(2, 300, 3000),
        ];
    }

    /**
     * Staff member on probation.
     */
    public function probation(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => Staff::STATUS_PROBATION,
        ]);
    }

    /**
     * Staff member who has left.
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => Staff::STATUS_TERMINATED,
        ]);
    }

    /**
     * Female staff member.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
        ]);
    }

    /**
     * Staff member with an English display name.
     */
    public function withEnglishName(): static
    {
        return $this->state(fn (array $attributes) => [
            'name_en' => Str::title(fake()->words(2, true)),
        ]);
    }
}
