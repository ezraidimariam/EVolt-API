<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\ChargingStation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+1 week');
        $endTime = (clone $startTime)->modify('+' . fake()->numberBetween(1, 8) . ' hours');

        return [
            'user_id' => User::factory(),
            'charging_station_id' => ChargingStation::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => fake()->randomElement(['en_cours', 'payee', 'annulee']),
        ];
    }

    /**
     * Indicate that the reservation is currently active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(2),
            'status' => 'en_cours',
        ]);
    }

    /**
     * Indicate that the reservation is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => now()->subHours(4),
            'end_time' => now()->subHours(2),
            'status' => 'payee',
        ]);
    }

    /**
     * Indicate that the reservation is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'annulee',
        ]);
    }
}
