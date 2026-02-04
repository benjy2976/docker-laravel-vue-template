# Contexto: Admin/Permissions

## Proposito
Administrar permisos y metadata de menus.

## Ruta y permisos
- Ruta: /admin/permissions
- Permisos: permissions.view|create|edit|delete

## Componentes
- List.vue
  - Usa usePermissionStore (pmsg) y permissionStore.get().
  - Acciones: editar y eliminar (segun permisos).
- Form.vue
  - Modal bootstrap con create/edit.
  - Permite configurar name, menu_label, menu_path, description e icon.
