import PermissionsIndex from '@/modules/admin/permissions/index.vue'

// Rutas para permisos
export const permissionRoutes = [
  {
    path      : '/permissions',
    name      : 'permissions.index',
    meta      : { requiresAuth: true, permissions: ['permissions.view'] },
    component : PermissionsIndex,
  },
]
