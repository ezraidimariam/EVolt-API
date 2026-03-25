<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChargingStation;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->station = ChargingStation::factory()->create();
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'stats' => [
                        'total_reservations',
                        'payee_reservations',
                        'en_cours_reservations',
                        'annulee_reservations',
                    ],
                    'last_reservations',
                ]);
    }

    public function test_user_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_dashboard_shows_correct_statistics()
    {
        // Create test reservations
        Reservation::factory()->create(['status' => 'en_cours']);
        Reservation::factory()->create(['status' => 'payee']);
        Reservation::factory()->create(['status' => 'annulee']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'total_reservations' => 3,
                    'payee_reservations' => 1,
                    'en_cours_reservations' => 1,
                    'annulee_reservations' => 1,
                ]);
    }

    public function test_dashboard_shows_last_reservations()
    {
        // Create 15 reservations to test the limit of 10
        Reservation::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
                ->assertJsonCount(10, 'last_reservations'); // Should return only 10
    }

    public function test_dashboard_includes_user_and_station_data()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'charging_station_id' => $this->station->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'user_id' => $this->user->id,
                    'charging_station_id' => $this->station->id,
                ]);
    }

    private function actingAs($user)
    {
        $token = $user->createToken('test-token')->plainTextToken;
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}
