<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->has('is_menu')) {
            $query->where('is_menu', filter_var($request->query('is_menu'), FILTER_VALIDATE_BOOL));
        }

        return $query->orderBy('sort_order')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'is_menu' => ['sometimes', 'boolean'],
            'menu_label' => ['nullable', 'string', 'max:255'],
            'menu_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:permissions,id'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['guard_name'] = $data['guard_name'] ?? 'web';
        $data['is_menu'] = $data['is_menu'] ?? false;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $permission = Permission::create($data);

        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        return $permission;
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'is_menu' => ['sometimes', 'boolean'],
            'menu_label' => ['nullable', 'string', 'max:255'],
            'menu_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:permissions,id'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['guard_name'] = $data['guard_name'] ?? $permission->guard_name ?? 'web';
        $data['is_menu'] = $data['is_menu'] ?? $permission->is_menu ?? false;
        $data['sort_order'] = $data['sort_order'] ?? $permission->sort_order ?? 0;

        $permission->update($data);

        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }
}
