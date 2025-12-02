<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * List all users (with optional pagination)
     */
    public function index(Request $request)
    {
        $items = User::orderBy('id', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json($items, 200);
    }

    /**
     * Create new user manually OR from Flutter registration
     * Accepts latitude, longitude, address
     */
    public function store(Request $request)
    {
        \Log::info("REGISTER API HIT", $request->all());
        $data = $request->validate([
            'firebase_uid'   => ['required', 'string', 'max:255', 'unique:users,firebase_uid'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'name'           => ['nullable', 'string', 'max:255'],
            'role'           => ['sometimes', 'string', 'max:50'],
            'email_verified' => ['sometimes', 'boolean'],

            // New fields
            'latitude'       => ['nullable', 'numeric'],
            'longitude'      => ['nullable', 'numeric'],
            'address'        => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {

            // 1️⃣ Save user including location
            $user = User::create([
                'firebase_uid'   => $data['firebase_uid'],
                'email'          => $data['email'],
                'name'           => $data['name'] ?? null,
                'role'           => $data['role'] ?? 'USER',
                'email_verified' => $data['email_verified'] ?? false,

                // Location fields
                'latitude'       => $data['latitude'] ?? null,
                'longitude'      => $data['longitude'] ?? null,
                'address'        => $data['address'] ?? null,
            ]);

            // 2️⃣ Optional — Create incident automatically when location exists
            if (isset($data['latitude']) && isset($data['longitude'])) {
                DB::table('incidents')->insert([
                    'title'       => 'New User Registered',
                    'description' => "{$data['name']} created an account",
                    'latitude'    => $data['latitude'],
                    'longitude'   => $data['longitude'],
                    'location'    => $data['address'] ?? null,
                    'project_id'  => 1, // Default project
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'user'    => $user,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to create user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single user
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Update or auto-create user by Firebase UID
     * Accepts latitude, longitude, address
     */
    public function update(Request $request, $uid)
    {
        $user = User::where('firebase_uid', $uid)->first();

        // Auto-create if first login
        if (!$user) {
            $user = User::create([
                'firebase_uid'   => $uid,
                'email'          => $request->input('email', ''),
                'name'           => $request->input('name', ''),
                'role'           => 'USER',
                'email_verified' => $request->boolean('email_verified', false),

                // Location fields
                'latitude'       => $request->input('latitude'),
                'longitude'      => $request->input('longitude'),
                'address'        => $request->input('address'),
            ]);
        }

        $data = $request->validate([
            'email'          => ['sometimes', 'email', 'max:255', Rule::unique('users','email')->ignore($user->id)],
            'name'           => ['sometimes', 'string', 'max:255'],
            'role'           => ['sometimes', 'string', 'max:50'],
            'email_verified' => ['sometimes', 'boolean'],

            // New fields
            'latitude'       => ['sometimes', 'numeric'],
            'longitude'      => ['sometimes', 'numeric'],
            'address'        => ['sometimes', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $user->update($data);

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'user'    => $user->fresh(),
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to update user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lookup user by Firebase UID
     */
    public function showByFirebase($uid)
    {
        $user = User::where('firebase_uid', $uid)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found', 'uid' => $uid], 404);
        }

        return response()->json([
            'firebase_uid'   => $user->firebase_uid,
            'email'          => $user->email,
            'name'           => $user->name,
            'role'           => $user->role,
            'email_verified' => $user->email_verified,
            'latitude'       => $user->latitude,
            'longitude'      => $user->longitude,
            'address'        => $user->address,
            'created_at'     => $user->created_at,
        ], 200);
    }

    /**
     * Update/auto-create user by Firebase UID
     * Accepts latitude, longitude, address
     */
    public function updateByFirebase(Request $request, $uid)
    {
        $user = User::where('firebase_uid', $uid)->first();

        if (!$user) {
            // FIRST TIME LOGIN → AUTO-CREATE USER + LOCATION
            $user = User::create([
                'firebase_uid'   => $uid,
                'email'          => $request->input('email', ''),
                'name'           => $request->input('name', ''),
                'role'           => 'USER',
                'email_verified' => $request->boolean('email_verified', false),

                // NEW LOCATION FIELDS
                'latitude'       => $request->input('latitude'),
                'longitude'      => $request->input('longitude'),
                'address'        => $request->input('address'),
            ]);
        }

        // Partial update
        $data = $request->only([
            'email',
            'name',
            'role',
            'email_verified',
            'latitude',
            'longitude',
            'address'
        ]);

        $user->update(array_filter($data, fn($v) => !is_null($v)));

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user->fresh(),
        ], 200);
    }

    /**
     * Delete user by Firebase UID
     */
    public function destroyByFirebase($uid)
    {
        $user = User::where('firebase_uid', $uid)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found', 'uid' => $uid], 404);
        }

        $user->delete();
        return response()->json(['deleted' => true], 200);
    }
}
