<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChargingStation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChargingStationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_user_can_view_available_stations()
    {
        ChargingStation::factory()->create(['is_available' => true]);
        ChargingStation::factory()->create(['is_available' => false]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/charging-stations');

        $response->assertStatus(200)
                ->assertJsonCount(1); // Only available stations
    }

    public function test_user_can_search_stations_by_location()
    {
        $station = ChargingStation::factory()->create([
            'latitude' => 33.5731,
            'longitude' => -7.5898,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/charging-stations/search?latitude=33.5731&longitude=-7.5898&radius=10');

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment([
                    'id' => $station->id,
                ]);
    }

    public function test_user_can_search_stations_by_connector_type()
    {
        $station = ChargingStation::factory()->create([
            'connector_type' => 'Type 2',
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/charging-stations/search?latitude=33.5731&longitude=-7.5898&connector_type=Type 2');

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment([
                    'connector_type' => 'Type 2',
                ]);
    }

    public function test_user_can_search_stations_by_minimum_power()
    {
        $station = ChargingStation::factory()->create([
            'power_kw' => 50.0,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/charging-stations/search?latitude=33.5731&longitude=-7.5898&min_power=40');

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment([
                    'power_kw' => 50.0,
                ]);
    }

    public function test_admin_can_create_station()
    {
        $stationData = [
            'name' => 'Test Station',
            'address' => 'Test Address',
            'latitude' => 33.5731,
            'longitude' => -7.5898,
            'connector_type' => 'Type 2',
            'power_kw' => 22.0,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/charging-stations', $stationData);

        $response->assertStatus(201)
                ->assertJsonFragment($stationData);

        $this->assertDatabaseHas('charging_stations', $stationData);
    }

    public function test_user_cannot_create_station()
    {
        $stationData = [
            'name' => 'Test Station',
            'address' => 'Test Address',
            'latitude' => 33.5731,
            'longitude' => -7.5898,
            'connector_type' => 'Type 2',
            'power_kw' => 22.0,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/admin/charging-stations', $stationData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_station()
    {
        $station = ChargingStation::factory()->create();

        $updateData = [
            'name' => 'Updated Station Name',
            'power_kw' => 25.0,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/charging-stations/{$station->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('charging_stations', [
            'id' => $station->id,
            'name' => 'Updated Station Name',
            'power_kw' => 25.0,
        ]);
    }

    public function test_user_cannot_update_station()
    {
        $station = ChargingStation::factory()->create();

        $response = $this->actingAs($this->user)
            ->putJson("/api/admin/charging-stations/{$station->id}", [
                'name' => 'Updated Station Name',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_station()
    {
        $station = ChargingStation::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/charging-stations/{$station->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('charging_stations', [
            'id' => $station->id,
        ]);
    }

    public function test_user_cannot_delete_station()
    {
        $station = ChargingStation::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/admin/charging-stations/{$station->id}");

        $response->assertStatus(403);
    }

    private function actingAs($user)
    {
        $token = $user->createToken('test-token')->plainTextToken;
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}
