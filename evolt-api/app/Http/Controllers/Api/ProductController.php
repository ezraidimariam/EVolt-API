<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChargingStation;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Get all products (charging stations)
    public function index()
    {
        $products = ChargingStation::all()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => '€' . number_format($product->power_kw * 10, 2), // Simple price calculation
                'category' => 'Charging Station',
                'description' => $product->address,
                'in_stock' => $product->is_available,
                'stock_status' => $product->is_available ? 'Available' : 'Unavailable',
                'power_kw' => $product->power_kw,
                'connector_type' => $product->connector_type
            ];
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $products,
            'count' => count($products)
        ]);
    }

    // Get single product
    public function show($id)
    {
        $product = ChargingStation::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => '€' . number_format($product->power_kw * 10, 2),
                'category' => 'Charging Station',
                'description' => $product->address,
                'in_stock' => $product->is_available,
                'stock_status' => $product->is_available ? 'Available' : 'Unavailable',
                'power_kw' => $product->power_kw,
                'connector_type' => $product->connector_type,
                'latitude' => $product->latitude,
                'longitude' => $product->longitude
            ]
        ]);
    }

    // Search products
    public function search($name)
    {
        $products = ChargingStation::where('name', 'like', "%{$name}%")
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => '€' . number_format($product->power_kw * 10, 2),
                    'category' => 'Charging Station',
                    'stock_status' => $product->is_available ? 'Available' : 'Unavailable',
                    'power_kw' => $product->power_kw
                ];
            });
        
        return response()->json([
            'success' => true,
            'message' => 'Products found',
            'data' => $products,
            'count' => count($products),
            'search_term' => $name
        ]);
    }

    // Create new product
    public function store(Request $request)
    {
        // Simple validation
        if (!$request->name || !$request->power_kw) {
            return response()->json([
                'success' => false,
                'message' => 'Name and power are required'
            ], 400);
        }
        
        try {
            $product = ChargingStation::create([
                'name' => $request->name,
                'address' => $request->address ?? 'Default Address',
                'latitude' => $request->latitude ?? 33.5731,
                'longitude' => $request->longitude ?? -7.5898,
                'connector_type' => $request->connector_type ?? 'Type 2',
                'power_kw' => $request->power_kw,
                'is_available' => $request->is_available ?? true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => '€' . number_format($product->power_kw * 10, 2),
                    'category' => 'Charging Station',
                    'power_kw' => $product->power_kw
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update product
    public function update(Request $request, $id)
    {
        $product = ChargingStation::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        try {
            $product->update([
                'name' => $request->name ?? $product->name,
                'address' => $request->address ?? $product->address,
                'power_kw' => $request->power_kw ?? $product->power_kw,
                'connector_type' => $request->connector_type ?? $product->connector_type,
                'is_available' => $request->is_available ?? $product->is_available
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => '€' . number_format($product->power_kw * 10, 2),
                    'category' => 'Charging Station',
                    'power_kw' => $product->power_kw
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete product
    public function destroy($id)
    {
        $product = ChargingStation::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        try {
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }
}
