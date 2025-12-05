import { h } from 'vue'

const RolesIndex = { name: 'RolesIndex', render: () => h('div', 'Roles') }
const RolesCreate = { name: 'RolesCreate', render: () => h('div', 'Create Role') }
const RolesEdit = { name: 'RolesEdit', render: () => h('div', 'Edit Role') }

// Rutas para roles
export const roleRoutes = [
  {
    path      : '/roles',
    name      : 'roles.index',
    meta      : { requiresAuth: true, permissions: ['roles.view'] },
    component : RolesIndex,
  },
  {
    path      : '/roles/create',
    name      : 'roles.create',
    meta      : { requiresAuth: true, permissions: ['roles.create'] },
    component : RolesCreate,
  },
  {
    path      : '/roles/:id/edit',
    name      : 'roles.edit',
    props     : true,
    meta      : { requiresAuth: true, permissions: ['roles.edit'] },
    component : RolesEdit,
  },
]
