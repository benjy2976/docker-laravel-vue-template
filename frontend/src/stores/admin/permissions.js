import { defineStore } from 'pinia'
import { Permission, createPermissionStore } from '@/core/admin/permission'

// Estado, getters y acciones adicionales para el store de permisos
const extraState = { /* ... */ }
const extraGetters = { /* ... */ }
const extraActions = {
  /* async ensureMenusLoaded() {
    if (!this.items.length) {
      await this.get({ params: { is_menu: true } })
    }
  }, */
}

// Store de opciones generado por el modelo usando el alias definido en el core
export const usePermissionStore = defineStore(
  Permission.alias,
  createPermissionStore(extraState, extraGetters, extraActions)
)
