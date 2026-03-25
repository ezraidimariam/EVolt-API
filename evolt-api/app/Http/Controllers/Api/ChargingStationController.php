<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChargingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChargingStationController extends Controller
{
    public function index(Request $request)
    {
        $stations = ChargingStation::where('is_available', true)->get();
        return response()->json($stations);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:1|max:50',
            'connector_type' => 'sometimes|in:Type 1,Type 2,CHAdeMO,CCS',
            'min_power' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = ChargingStation::where('is_available', true);

        // Filter by connector type if specified
        if ($request->has('connector_type')) {
            $query->where('connector_type', $request->connector_type);
        }

        // Filter by minimum power if specified
        if ($request->has('min_power')) {
            $query->where('power_kw', '>=', $request->min_power);
        }

        // Get all stations first (simplified approach for SQLite)
        $stations = $query->get();

        // Filter by radius if specified (basic calculation)
        if ($request->has('radius')) {
            $userLat = $request->latitude;
            $userLng = $request->longitude;
            $radius = $request->radius;

            $stations = $stations->filter(function ($station) use ($userLat, $userLng, $radius) {
                $distance = $this->calculateDistance($userLat, $userLng, $station->latitude, $station->longitude);
                return $distance <= $radius;
            });
        }

        return response()->json($stations->values());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'connector_type' => 'required|in:Type 1,Type 2,CHAdeMO,CCS',
            'power_kw' => 'required|numeric|min:1|max:350',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $station = ChargingStation::create([
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'connector_type' => $request->connector_type,
            'power_kw' => $request->power_kw,
            'is_available' => $request->is_available ?? true,
        ]);

        return response()->json($station, 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'connector_type' => 'sometimes|in:Type 1,Type 2,CHAdeMO,CCS',
            'power_kw' => 'sometimes|numeric|min:1|max:350',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $station = ChargingStation::findOrFail($id);
        $station->update($request->all());

        return response()->json($station);
    }

    public function destroy($id)
    {
        $station = ChargingStation::findOrFail($id);
        $station->delete();

        return response()->json(['message' => 'Charging station deleted successfully']);
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
