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
     * Create new user manually (mainly for admin use)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'firebase_uid'   => ['required', 'string', 'max:255', 'unique:users,firebase_uid'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'name'           => ['nullable', 'string', 'max:255'],
            'role'           => ['sometimes', 'string', 'max:50'],
            'email_verified' => ['sometimes', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create($data);
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
            ]);
        }

        $data = $request->validate([
            'email'          => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'name'           => ['sometimes', 'string', 'max:255'],
            'role'           => ['sometimes', 'string', 'max:50'],
            'email_verified' => ['sometimes', 'boolean'],
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
     * Delete a user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['deleted' => true], 200);
    }

   // For showByFirebase
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
        'created_at'     => $user->created_at,
    ], 200);
}

// For updateByFirebase
public function updateByFirebase(Request $request, $uid)
{
    $user = User::where('firebase_uid', $uid)->first();

    if (!$user) {
        // Auto-create user on first login if not found
        $user = User::create([
            'firebase_uid'   => $uid,
            'email'          => $request->input('email', ''),
            'name'           => $request->input('name', ''),
            'role'           => 'USER',
            'email_verified' => $request->boolean('email_verified', false),
        ]);
    }

    $data = $request->only(['email', 'name', 'role', 'email_verified']);
    $user->update(array_filter($data, fn($v) => !is_null($v)));

    return response()->json([
        'message' => 'User updated successfully',
        'user'    => $user->fresh(),
    ], 200);
}

// Optional destroyByFirebase
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
