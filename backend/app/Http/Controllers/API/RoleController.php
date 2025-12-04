<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query()->with('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->filled('guard_name')) {
            $query->where('guard_name', $request->query('guard_name'));
        }

        return $query->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $data['guard_name'] = $data['guard_name'] ?? 'web';

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return $role->load('permissions');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $data['guard_name'] = $data['guard_name'] ?? $role->guard_name ?? 'web';

        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ]);

        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions'] ?? []);
        }

        return response()->json($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
