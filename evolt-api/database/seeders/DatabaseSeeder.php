<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ChargingStation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@evolt.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@evolt.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create charging stations
        ChargingStation::create([
            'name' => 'Station Casablanca Centre',
            'address' => 'Casablanca, Morocco',
            'latitude' => 33.5731,
            'longitude' => -7.5898,
            'connector_type' => 'Type 2',
            'power_kw' => 22.0,
            'is_available' => true,
        ]);

        ChargingStation::create([
            'name' => 'Station Rabat Agdal',
            'address' => 'Rabat, Morocco',
            'latitude' => 34.0133,
            'longitude' => -6.8326,
            'connector_type' => 'CCS',
            'power_kw' => 50.0,
            'is_available' => true,
        ]);

        ChargingStation::create([
            'name' => 'Station Marrakech Gueliz',
            'address' => 'Marrakech, Morocco',
            'latitude' => 31.6295,
            'longitude' => -7.9811,
            'connector_type' => 'CHAdeMO',
            'power_kw' => 43.0,
            'is_available' => true,
        ]);
    }
}
