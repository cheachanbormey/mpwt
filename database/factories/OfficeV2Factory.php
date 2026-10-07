<?php

namespace Database\Factories;

use App\Models\DepartmentV2;
use App\Models\OfficeV2;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfficeV2>
 */
class OfficeV2Factory extends Factory
{
    protected $model = OfficeV2::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dep_id' => DepartmentV2::factory(),
            'office_code' => 'OFF-'.fake()->unique()->numerify('###'),
            'office_name' => 'ការិយាល័យសារីរុង '.fake()->unique()->numberBetween(1, 999),
            'location' => fake()->randomElement([
                'រាជធានីភ្នំពេញ',
                'ខេត្តកំពង',
                'ខេត្តពោគ',
                'ខេត្តសៀមរាប',
            ]),
            'description' => fake()->sentence(8),
            'status' => OfficeV2::STATUS_ACTIVE,
        ];
    }

    /**
     * Office attached to an existing department.
     */
    public function forDepartment(DepartmentV2 $department): static
    {
        return $this->state(fn (array $attributes) => [
            'dep_id' => $department->getKey(),
        ]);
    }

    /**
     * Inactive office.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OfficeV2::STATUS_INACTIVE,
        ]);
    }
}
