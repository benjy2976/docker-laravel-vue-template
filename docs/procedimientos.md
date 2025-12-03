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

TODO: Añadir pasos de build/preview frontend y despliegue.
