# API

- Rutas principales (protegidas con `auth:sanctum` salvo auth/reset):
  - `POST /login`, `POST /register`, `POST /forgot-password`, `POST /reset-password`, `POST /logout`.
  - `GET /api/user` (carga roles y permisos).
  - `apiResource /api/projects` (CRUD proyectos).
  - `apiResource /api/permissions` (CRUD permisos; middleware de permisos por acción).
- Campos extra en `permissions` para menús dinámicos:
  - `is_menu`, `menu_label`, `menu_path`, `icon`, `parent_id`, `sort_order`.
- Notas de uso:
  - Para filtrar permisos por nombre: `?search=abc` o `?name=abc`.
  - Para menús: `?is_menu=true` y usar jerarquía `parent_id` + `sort_order`.

TODO: Documentar respuestas/errores y ejemplos de payload para cada endpoint.
