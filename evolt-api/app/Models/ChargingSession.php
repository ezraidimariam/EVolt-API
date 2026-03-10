<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargingSession extends Model
{
    protected $fillable = [
        'user_id',
        'charging_station_id',
        'reservation_id',
        'start_time',
        'end_time',
        'energy_delivered_kwh',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'energy_delivered_kwh' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargingStation()
    {
        return $this->belongsTo(ChargingStation::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->end_time = now();
        $this->save();
    }
}
