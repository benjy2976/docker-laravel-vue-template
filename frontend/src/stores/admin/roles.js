import { defineStore } from 'pinia'
import { Role, createRoleStore } from '@/core/admin/role'

// Estado, getters y acciones adicionales para el store de roles
const extraState = { /* ... */ }
const extraGetters = { /* ... */ }
const extraActions = { /* ... */ }

// Store de opciones generado por el modelo usando el alias definido en el core
export const useRoleStore = defineStore(
  Role.alias,
  createRoleStore(extraState, extraGetters, extraActions)
)
