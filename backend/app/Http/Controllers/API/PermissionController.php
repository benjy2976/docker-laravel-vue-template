<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Configura middleware de permisos por acción.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:permissions.view')->only(['index', 'show']);
        $this->middleware('permission:permissions.create')->only(['store']);
        $this->middleware('permission:permissions.edit')->only(['update']);
        $this->middleware('permission:permissions.delete')->only(['destroy']);
    }

    /**
     * Lista permisos con filtros opcionales.
     *
     * @param Request $request Filtros de búsqueda.
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filtro genérico por término
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        // Filtro por nombre
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        // Filtro por flag de menú
        if ($request->has('is_menu')) {
            $query->where('is_menu', filter_var($request->query('is_menu'), FILTER_VALIDATE_BOOL));
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * Crea un permiso.
     *
     * @param Request $request Datos del permiso.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación falla.
     */
    public function store(Request $request)
    {
        // Validar datos del permiso
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
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

    /**
     * Muestra un permiso.
     *
     * @param Permission $permission Permiso a consultar.
     * @return Permission
     */
    public function show(Permission $permission)
    {
        return $permission;
    }

    /**
     * Actualiza un permiso.
     *
     * @param Request     $request    Datos a actualizar.
     * @param Permission  $permission Permiso objetivo.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación falla.
     */
    public function update(Request $request, Permission $permission)
    {
        // Validar datos del permiso a actualizar
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
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

    /**
     * Elimina un permiso.
     *
     * @param Permission $permission Permiso a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }
}
