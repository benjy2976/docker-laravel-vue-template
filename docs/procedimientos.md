# Procedimientos

- Arranque con Docker:
  ```bash
  docker compose up --build
  ```
  Backend en 8000, frontend en 5173, mailpit en 8025.
- Migraciones/seeders:
  ```bash
  docker compose exec backend php artisan migrate
  docker compose exec backend php artisan db:seed
  ```
- Limpieza de caché (Laravel):
  ```bash
  docker compose exec backend php artisan config:clear
  docker compose exec backend php artisan cache:clear
  ```
- Flujo de autenticación Sanctum:
  1) GET `/sanctum/csrf-cookie`
  2) POST `/login` o `/register`
  3) GET `/api/user`
- Memoria: cualquier cambio en convenciones/arquitectura debe proponerse como actualización en `docs/` o `.github/` y esperar aprobación.

## Generación de módulos
- Backend:
  - Crear controlador con rutas `apiResource` y añadir en `backend/routes/api.php` solo si no existe; si hay rutas previas/colisiones, detenerse y pedir confirmación.
  - Generar migraciones/seeders solo si el módulo es nuevo y no existen; si ya hay, no tocarlos salvo instrucción explícita.
- Frontend:
  - Core model en `frontend/src/core/...` con alias/route/default y export del modelo + `createModelStore`.
  - Store Pinia en `frontend/src/store/...` reutilizando el store generado por el core model.
  - Adaptadores de respuesta/paginación vienen desde la librería pmsg (no reimplementar aquí).

TODO: Añadir pasos de build/preview frontend y despliegue.
