# Arquitectura del proyecto

- Frontend: Vite + Vue 3, modularizado en `src/layout`, `src/pages`, stores en `src/store`, modelos pmsg en `src/core`.
- Backend: Laravel 11 (API REST), Sanctum para auth, Spatie Permission para roles/permisos.
- Contenedores: docker-compose con servicios db (MySQL), backend (php-fpm + nginx), frontend (Vite), mailpit para correo de prueba.
- Convención de rutas API: `apiResource` para módulos (ej. `projects`, `permissions`), protegido con `auth:sanctum` y roles/permisos según corresponda.
- Menús dinámicos: campos de permisos (`is_menu`, `menu_label`, `menu_path`, `parent_id`, `sort_order`) permiten construir sidebar.

TODO: Detallar diagramas de componentes y flujo de datos.
