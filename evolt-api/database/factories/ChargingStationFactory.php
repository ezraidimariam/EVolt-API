<?php

namespace Database\Factories;

use App\Models\ChargingStation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChargingStation>
 */
class ChargingStationFactory extends Factory
{
    protected $model = ChargingStation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Charging Station',
            'address' => fake()->address(),
            'latitude' => fake()->latitude(31, 35), // Morocco latitude range
            'longitude' => fake()->longitude(-10, -5), // Morocco longitude range
            'connector_type' => fake()->randomElement(['Type 1', 'Type 2', 'CHAdeMO', 'CCS']),
            'power_kw' => fake()->randomFloat(1, 7, 150), // Between 7 and 150 kW
            'is_available' => fake()->boolean(80), // 80% chance of being available
        ];
    }
}
