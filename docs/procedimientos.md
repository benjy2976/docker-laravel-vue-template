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
  - Estándar de store Pinia para modelos: `defineStore(model.alias, createModelStore())`; sin `reactive` extra ni doble alias, añadir overrides de state/getters/actions vacios para que el usuario pueda cargarlos, segun el siguient eejemplo
```js
import { defineStore } from 'pinia'
import { Role, createRoleStore } from '@/core/auth/role'
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
  - Adaptadores de respuesta/paginación los gestiona la librería pmsg; solo implementar manualmente si se solicita de forma explícita y en los lugares indicados.

TODO: Añadir pasos de build/preview frontend y despliegue.
