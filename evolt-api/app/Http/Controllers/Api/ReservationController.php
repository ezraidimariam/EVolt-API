<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ChargingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'charging_station_id' => 'required|exists:charging_stations,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chargingStation = ChargingStation::findOrFail($request->charging_station_id);

        // Check if station is available during the requested time
        if (!$chargingStation->isAvailableAt($request->start_time, $request->end_time)) {
            return response()->json(['message' => 'Station not available during this time'], 422);
        }

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'charging_station_id' => $request->charging_station_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'en_cours',
        ]);

        return response()->json($reservation->load(['user', 'chargingStation']), 201);
    }

    public function myReservations(Request $request)
    {
        $reservations = $request->user()
            ->reservations()
            ->with('chargingStation')
            ->latest()
            ->get();

        return response()->json($reservations);
    }

    public function pay(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check if reservation belongs to authenticated user
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if status is en_cours
        if ($reservation->status !== 'en_cours') {
            return response()->json(['message' => 'Reservation cannot be paid'], 422);
        }

        $reservation->update(['status' => 'payee']);

        return response()->json($reservation->load(['user', 'chargingStation']));
    }

    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check if reservation belongs to authenticated user
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->update(['status' => 'annulee']);

        return response()->json($reservation->load(['user', 'chargingStation']));
    }

    public function dashboard(Request $request)
    {
        $stats = [
            'total_reservations' => Reservation::count(),
            'payee_reservations' => Reservation::where('status', 'payee')->count(),
            'en_cours_reservations' => Reservation::where('status', 'en_cours')->count(),
            'annulee_reservations' => Reservation::where('status', 'annulee')->count(),
        ];

        $lastReservations = Reservation::with(['user', 'chargingStation'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'last_reservations' => $lastReservations
        ]);
    }
}
