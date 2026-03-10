<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargingStation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'connector_type',
        'power_kw',
        'is_available',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'power_kw' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function chargingSessions()
    {
        return $this->hasMany(ChargingSession::class);
    }

    public function isAvailableAt($startTime, $endTime)
    {
        return !$this->reservations()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }
}
