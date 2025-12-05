import { roleRoutes } from './roles'
import { permissionRoutes } from './permissions'

export const adminRoutes = [
  ...roleRoutes,
  ...permissionRoutes,
]
