# Contexto: Admin/Roles

## Proposito
Administrar roles del sistema y sus permisos asociados.

## Ruta y permisos
- Ruta: /admin/roles
- Permisos: roles.view|create|edit|delete

## Componentes
- List.vue
  - Usa useRoleStore (pmsg) y roleStore.get().
  - Muestra permisos como lista de nombres.
  - Acciones: editar y eliminar (segun permisos).
- Form.vue
  - Modal bootstrap con create/edit.
  - Permite asignar permisos a un rol.
