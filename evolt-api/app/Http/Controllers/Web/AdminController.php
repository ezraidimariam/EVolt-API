<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChargingStation;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_reservations' => Reservation::count(),
            'payee_reservations' => Reservation::where('status', 'payee')->count(),
            'en_cours_reservations' => Reservation::where('status', 'en_cours')->count(),
            'annulee_reservations' => Reservation::where('status', 'annulee')->count(),
            'total_stations' => ChargingStation::count(),
            'active_stations' => ChargingStation::where('is_available', true)->count(),
        ];

        $lastReservations = Reservation::with(['user', 'chargingStation'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'lastReservations'));
    }

    public function stations()
    {
        $stations = ChargingStation::latest()->get();
        return view('admin.stations', compact('stations'));
    }

    public function createStation()
    {
        return view('admin.create-station');
    }

    public function storeStation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'connector_type' => 'required|in:Type 1,Type 2,CHAdeMO,CCS',
            'power_kw' => 'required|numeric|min:1|max:350',
            'is_available' => 'sometimes|boolean',
        ]);

        ChargingStation::create($validated);

        return redirect()->route('admin.stations')
            ->with('success', 'Station créée avec succès!');
    }

    public function editStation($id)
    {
        $station = ChargingStation::findOrFail($id);
        return view('admin.edit-station', compact('station'));
    }

    public function updateStation(Request $request, $id)
    {
        $station = ChargingStation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'connector_type' => 'sometimes|in:Type 1,Type 2,CHAdeMO,CCS',
            'power_kw' => 'sometimes|numeric|min:1|max:350',
            'is_available' => 'sometimes|boolean',
        ]);

        $station->update($validated);

        return redirect()->route('admin.stations')
            ->with('success', 'Station mise à jour avec succès!');
    }

    public function deleteStation($id)
    {
        $station = ChargingStation::findOrFail($id);
        $station->delete();

        return redirect()->route('admin.stations')
            ->with('success', 'Station supprimée avec succès!');
    }
}
