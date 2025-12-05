import RolesIndex from '@/modules/admin/roles/index.vue'
// Rutas para roles
export const roleRoutes = [
  {
    path      : '/roles',
    name      : 'roles.index',
    meta      : { requiresAuth: true, permissions: ['roles.view'] },
    component : RolesIndex,
  },
]
