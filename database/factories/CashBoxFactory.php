<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashBox>
 */
class CashBoxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'صندوق - ' . $this->faker->city,
            'location' => $this->faker->streetAddress,
            'manager' => $this->faker->name,
        ];
    }
}
