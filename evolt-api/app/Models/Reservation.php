<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'charging_station_id',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargingStation()
    {
        return $this->belongsTo(ChargingStation::class);
    }

    public function chargingSession()
    {
        return $this->hasOne(ChargingSession::class);
    }

    public function canBeModified()
    {
        return in_array($this->status, ['en_cours']) && 
               $this->start_time > now();
    }
}
