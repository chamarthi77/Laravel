<?php


namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user(); // ✅ CORRECT

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized (no user)',
            ], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Unauthorized (role mismatch)',
                'required' => $roles,
                'actual' => $user->role,
            ], 403);
        }

        return $next($request);
    }
}
