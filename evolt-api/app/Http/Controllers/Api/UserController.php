<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'created_at' => $user->created_at->format('d/m/Y H:i')
            ];
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users,
            'count' => count($users)
        ]);
    }

    // Get single user
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'created_at' => $user->created_at->format('d/m/Y H:i')
            ]
        ]);
    }

    // Create new user (simple version)
    public function store(Request $request)
    {
        // Simple validation
        if (!$request->name || !$request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Name and email are required'
            ], 400);
        }
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt('password123'), // Default password for demo
                'role' => $request->role ?? 'user'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }
}
