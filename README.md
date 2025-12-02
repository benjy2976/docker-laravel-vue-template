# Template Docker

Contenedores listos para desarrollar con Laravel en `backend` y Vue 3 + Vite en `frontend`.

- Backend + PHP-FPM detrás de Nginx en `http://localhost:8000`.
- Frontend con servidor Vite en `http://localhost:5173`.
- Base de datos MySQL expuesta en `localhost:3307` (db:3306 dentro del cluster).

## Puesta en marcha

1. En `backend/`, copia el ejemplo: `cp .env.docker.example .env` y ajusta llaves/credenciales si hace falta.
2. Construye y levanta todo desde la raíz: `docker compose up --build`.  
   Composer y npm corren dentro de los contenedores antes de iniciar servicios.
3. Genera la APP_KEY (solo la primera vez): `docker compose exec backend php artisan key:generate`.
4. Ejecuta migraciones cuando la base esté lista: `docker compose exec backend php artisan migrate`.

## Notas útiles

- `VITE_BACKEND_URL` se inyecta desde `docker-compose.yml` (puerto 8000). Ajusta si sirves el backend en otro host.
- Los directorios del código se montan como volumen para hot reload. `node_modules` vive dentro del contenedor frontend.
- Las credenciales por defecto de la base son `template/template` (root/root en root).
- El build usa tu `UID/GID` del shell (por defecto 1000) para evitar problemas de permisos en `backend`.
