<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Configura permisos por acción.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['store']);
        $this->middleware('permission:projects.edit')->only(['update']);
        $this->middleware('permission:projects.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        // Listar proyectos más recientes primero
        $projects = Project::orderByDesc('created_at')->paginate(10);

        return response()->json($projects);
    }

    /**
     * Crea un proyecto.
     *
     * @param Request $request Datos validados del proyecto.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación falla.
     */
    public function store(Request $request)
    {
        // Validar payload y persistir proyecto
        $data = $this->validateData($request);

        $project = Project::create($data);

        return response()->json($project, Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        return response()->json($project);
    }

    /**
     * Actualiza un proyecto.
     *
     * @param Request $request Datos a actualizar.
     * @param Project $project Proyecto objetivo.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación falla.
     */
    public function update(Request $request, Project $project)
    {
        // Validar y actualizar datos del proyecto existente
        $data = $this->validateData($request);

        $project->update($data);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        // Eliminar proyecto
        $project->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Valida el payload estándar de proyectos.
     *
     * @param Request $request Request con datos a validar.
     * @return array Datos validados.
     */
    protected function validateData(Request $request): array
    {
        // Reglas estándar de validación para proyectos
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:pending,in_progress,done'],
        ]);
    }
}
