<?php

namespace Database\Factories;

use App\Models\Funder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'مشروع: ' . $this->faker->bs,
            'funder_id' => Funder::inRandomOrder()->first()->id,
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('+1 year', '+3 years'),
            'value' => 0,
            'currency' => 'USD',
            'description' => $this->faker->paragraph,
            'is_default' => false,
        ];
    }
}
