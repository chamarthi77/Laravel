<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $items = Permission::orderBy('id', 'asc')->paginate($request->integer('per_page', 50));
        return response()->json($items, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:permissions,name'],
            'description' => ['nullable', 'string'],
        ]);
        $perm = Permission::create($data);
        return response()->json($perm, 201);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission, 200);
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:50', Rule::unique('permissions', 'name')->ignore($permission->id)],
            'description' => ['nullable', 'string'],
        ]);
        $permission->update($data);
        return response()->json($permission, 200);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['deleted' => true], 200);
    }
}

