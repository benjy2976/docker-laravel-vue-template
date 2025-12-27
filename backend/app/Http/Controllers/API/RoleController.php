<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Configura middleware de permisos por acción.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['store']);
        $this->middleware('permission:roles.edit')->only(['update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    /**
     * Lista roles (con permisos) con filtros opcionales.
     *
     * @param Request $request Filtros de búsqueda.
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $query = Role::query()->with('permissions');

        // Búsqueda general por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        // Filtro por nombre exacto/parcial
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        // Filtro por guard_name
        if ($request->filled('guard_name')) {
            $query->where('guard_name', $request->query('guard_name'));
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Crea un rol.
     *
     * @param Request $request Datos y permisos del rol.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación falla.
     */
    public function store(Request $request)
    {
        // Validar datos del rol y permisos asociados
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

        // Sincronizar permisos si se enviaron
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return response()->json($role->load('permissions'), 201);
    }

    /**
     * Muestra un rol con permisos.
     *
     * @param Role $role Rol a consultar.
     * @return Role
     */
    public function show(Role $role)
    {
        return $role->load('permissions');
    }

    /**
     * Actualiza un rol y sus permisos.
     *
     * @param Request $request Datos a actualizar.
     * @param Role    $role    Rol objetivo.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación falla.
     */
    public function update(Request $request, Role $role)
    {
        // Validar datos del rol
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

        // Actualizar permisos si viene el campo
        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions($data['permissions'] ?? []);
        }

        return response()->json($role->load('permissions'));
    }

    /**
     * Elimina un rol.
     *
     * @param Role $role Rol a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
