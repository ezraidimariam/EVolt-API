<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChargingStation;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->station = ChargingStation::factory()->create(['is_available' => true]);
    }

    public function test_user_can_create_reservation()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', [
                'charging_station_id' => $this->station->id,
                'start_time' => now()->addHour()->toDateTimeString(),
                'end_time' => now()->addHours(3)->toDateTimeString(),
            ]);

        $response->assertStatus(201)
                ->assertJsonFragment([
                    'user_id' => $this->user->id,
                    'charging_station_id' => $this->station->id,
                    'status' => 'en_cours',
                ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);
    }

    public function test_user_can_view_their_reservations()
    {
        // Create reservations for the user
        Reservation::factory()->create([
            'user_id' => $this->user->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/mes-reservations');

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment([
                    'user_id' => $this->user->id,
                ]);
    }

    public function test_user_cannot_view_other_users_reservations()
    {
        $otherUser = User::factory()->create();
        
        // Create reservation for other user
        Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/mes-reservations');

        $response->assertStatus(200)
                ->assertJsonCount(0);
    }

    public function test_user_can_pay_reservation()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservation->id}/pay");

        $response->assertStatus(200)
                ->assertJsonFragment(['status' => 'payee']);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'payee',
        ]);
    }

    public function test_user_cannot_pay_other_users_reservation()
    {
        $otherUser = User::factory()->create();
        
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservation->id}/pay");

        $response->assertStatus(403);
    }

    public function test_user_can_cancel_reservation()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservation->id}/cancel");

        $response->assertStatus(200)
                ->assertJsonFragment(['status' => 'annulee']);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'annulee',
        ]);
    }

    public function test_user_cannot_cancel_other_users_reservation()
    {
        $otherUser = User::factory()->create();
        
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'charging_station_id' => $this->station->id,
            'status' => 'en_cours',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/reservations/{$reservation->id}/cancel");

        $response->assertStatus(403);
    }

    public function test_cannot_create_reservation_for_unavailable_station()
    {
        $unavailableStation = ChargingStation::factory()->create(['is_available' => false]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', [
                'charging_station_id' => $unavailableStation->id,
                'start_time' => now()->addHour()->toDateTimeString(),
                'end_time' => now()->addHours(3)->toDateTimeString(),
            ]);

        $response->assertStatus(422);
    }

    public function test_cannot_create_reservation_with_invalid_dates()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/reservations', [
                'charging_station_id' => $this->station->id,
                'start_time' => now()->addHours(3)->toDateTimeString(),
                'end_time' => now()->addHour()->toDateTimeString(), // End before start
            ]);

        $response->assertStatus(422);
    }

    private function actingAs($user)
    {
        $token = $user->createToken('test-token')->plainTextToken;
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}
