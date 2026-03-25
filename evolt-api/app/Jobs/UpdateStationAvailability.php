<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Models\ChargingStation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateStationAvailability implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting station availability update job');

        // Find all reservations that have ended but still have 'en_cours' status
        $expiredReservations = Reservation::where('status', 'en_cours')
            ->where('end_time', '<', now())
            ->get();

        foreach ($expiredReservations as $reservation) {
            Log::info("Processing expired reservation: {$reservation->id}");
            
            // Update reservation status to 'completed'
            $reservation->update(['status' => 'completed']);
            
            // Check if station has other active reservations
            $station = $reservation->chargingStation;
            $hasActiveReservations = $station->reservations()
                ->where('status', 'en_cours')
                ->where('end_time', '>', now())
                ->exists();

            // Update station availability
            if (!$hasActiveReservations) {
                $station->update(['is_available' => true]);
                Log::info("Station {$station->id} marked as available");
            }
        }

        // Also check for stations that should be unavailable (have active reservations)
        $stations = ChargingStation::where('is_available', true)->get();
        
        foreach ($stations as $station) {
            $hasActiveReservations = $station->reservations()
                ->where('status', 'en_cours')
                ->where('start_time', '<=', now())
                ->where('end_time', '>', now())
                ->exists();

            if ($hasActiveReservations) {
                $station->update(['is_available' => false]);
                Log::info("Station {$station->id} marked as unavailable due to active reservation");
            }
        }

        Log::info('Station availability update job completed');
    }
}
