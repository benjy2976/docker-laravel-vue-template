# Procedimientos

- Arranque con Docker:
  ```bash
  docker compose up --build
  ```
  Backend en 8000, frontend en 5173, mailpit en 8025.
- Migraciones/seeders:
  - En esta etapa del proyecto, unificar cambios en la migracion que crea la tabla (no crear migraciones adicionales) y pedir aprobacion antes de crear una nueva.
  ```bash
  docker compose exec backend php artisan migrate
  docker compose exec backend php artisan db:seed
  ```

## Registro de tablas en scripts de backup
- Cuando crees una nueva tabla (migración nueva), debes agregarla al script de modelos de backup:
  - Archivo: `scripts/db_models_pg.sh`
  - Agregar la tabla en el modelo correspondiente según su namespace:
    - `app/Models/` (base) → modelo `auth`
    - `app/Models/Project` → modelo `projects`
    - Spatie roles/permisos → modelo `permissions`
  - También agregar la tabla en el modelo `full` (respaldo completo).
  - Verificar que el nombre de la tabla coincida exactamente con el definido en la migración.

## Backups de base de datos (por modelo)
- Listar modelos disponibles:
  ```bash
  ./scripts/db_backup_pg.sh --list-models
  ```
- Crear backup por modelo:
  ```bash
  ./scripts/db_backup_pg.sh --model projects
  ./scripts/db_backup_pg.sh --model auth
  ```
- Restaurar un modelo:
  ```bash
  ./scripts/db_restore_pg.sh <backup_dir> data --model projects
  ./scripts/db_restore_pg.sh <backup_dir> all --model auth
  ```
- Compatibilidad entre versiones: si falta una tabla en el destino se omite (usar `--strict` para fallar).
- `--truncate` elimina datos previos del modelo con `CASCADE` (usar con cuidado).

## Deploy frontend (cache)
- Los assets generados por Vite incluyen hash; evitar cachear `index.html` para propagar cambios sin hard refresh.
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
  - Store Pinia en `frontend/src/stores/...` reutilizando el store generado por el core model.
  - Estándar de store Pinia para modelos: `defineStore(model.alias, createModelStore())`; sin `reactive` extra ni doble alias, añadir overrides de state/getters/actions vacios para que el usuario pueda cargarlos, segun el siguient eejemplo
```js
import { defineStore } from 'pinia'
import { Role, createRoleStore } from '@core/admin/role'
// Estado, getters y acciones adicionales para el store de roles
const extraState = { /* ... */ }
const extraGetters = { /* ... */ }
const extraActions = { /* ... */ }
// Store de opciones generado por el modelo usando el alias definido en el core
export const useRoleStore = defineStore(
  Role.alias,
  createRoleStore(extraState, extraGetters, extraActions)
)
```
  - Adaptadores de respuesta/paginación los gestiona la librería @benjy2976/pmsg; solo implementar manualmente si se solicita de forma explícita y en los lugares indicados.
  - Métodos custom (sin store): cuando se necesita un endpoint puntual sin estado, crear el core en `frontend/src/core/...` y exponer el método en `methods`. No construir rutas manualmente en el componente si existe core.
  - TODO: agregar un ejemplo real cuando exista un endpoint puntual sin store en template.

TODO: Añadir pasos de build/preview frontend y despliegue.
