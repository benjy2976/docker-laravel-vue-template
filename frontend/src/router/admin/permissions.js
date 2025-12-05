import { h } from 'vue'

const PermissionsIndex = { name: 'PermissionsIndex', render: () => h('div', 'Permissions') }
const PermissionsCreate = { name: 'PermissionsCreate', render: () => h('div', 'Create Permission') }
const PermissionsEdit = { name: 'PermissionsEdit', render: () => h('div', 'Edit Permission') }

// Rutas para permisos
export const permissionRoutes = [
  {
    path      : '/permissions',
    name      : 'permissions.index',
    meta      : { requiresAuth: true, permissions: ['permissions.view'] },
    component : PermissionsIndex,
  },
  {
    path      : '/permissions/create',
    name      : 'permissions.create',
    meta      : { requiresAuth: true, permissions: ['permissions.create'] },
    component : PermissionsCreate,
  },
  {
    path      : '/permissions/:id/edit',
    name      : 'permissions.edit',
    props     : true,
    meta      : { requiresAuth: true, permissions: ['permissions.edit'] },
    component : PermissionsEdit,
  },
]
