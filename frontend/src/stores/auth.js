import { defineStore } from 'pinia'
import api from '@/axios'

let fetchPromise = null

export const useAuthStore = defineStore('auth', {
  state : () => ({
    user        : null,
    initialized : false,
  }),
  getters : {
    // Indica si hay sesion activa.
    isAuthenticated : (state) => !!state.user,
    // Construye el menu lateral en base a permisos con flag is_menu.
    menuSections    : (state) => {
      const perms = state.user?.permissions || []
      const menuItems = perms.filter(p => p.is_menu)

      if (!menuItems.length) return []

      const byParent = menuItems.reduce((acc, item) => {
        const key = item.parent_id || 'root'
        acc[key] = acc[key] || []
        acc[key].push(item)
        return acc
      }, {})

      const sortByOrder = (a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0)
      const roots = (byParent.root || []).sort(sortByOrder)

      return roots.map(root => ({
        title    : root.menu_label || root.name,
        icon     : root.icon || 'bi-circle',
        to       : root.menu_path || '#',
        children : (byParent[root.id] || [])
          .sort(sortByOrder)
          .map(child => ({
            label : child.menu_label || child.name,
            to    : child.menu_path || '#',
            icon  : child.icon || 'bi-dot',
          })),
      }))
    },
    // Verifica permiso por nombre.
    can : (state) => (permission) => {
      const perms = state.user?.permissions || []
      return perms.some(p => p.name === permission)
    },
  },
  actions : {
    // Obtiene el usuario autenticado y sus permisos.
    async fetchUser() {
      if (fetchPromise) return fetchPromise
      fetchPromise = (async () => {
        try {
          const { data } = await api.get('/api/user')
          this.user = data
        } finally {
          this.initialized = true
        }
      })()
      try {
        return await fetchPromise
      } finally {
        fetchPromise = null
      }
    },
    // Login con Sanctum y carga de usuario.
    async login(email, password, remember = false) {
      await api.get('/sanctum/csrf-cookie')
      await api.post('/login', { email, password, remember })
      await this.fetchUser()
    },
    // Registro con Sanctum e inicio de sesion.
    async register(nameOrPayload, email, password, passwordConfirmation) {
      await api.get('/sanctum/csrf-cookie')
      const payload = typeof nameOrPayload === 'object'
        ? nameOrPayload
        : {
          name                  : nameOrPayload,
          email                 : email,
          password              : password,
          password_confirmation : passwordConfirmation,
        }
      await api.post('/register', payload)
      await this.fetchUser()
    },
    // Cierra sesion y limpia el estado local.
    async logout() {
      await api.post('/logout')
      this.user = null
      this.initialized = true
    },
    // Actualiza perfil y refresca datos del usuario.
    async updateProfile(payload) {
      await api.put('/api/user/profile', payload)
      await this.fetchUser()
    },
    // Actualiza password (no cambia el estado local).
    async updatePassword(payload) {
      await api.put('/api/user/password', payload)
    },
  },
})

// Retorna el store de autenticacion; no retorna nada extra.
export function useAuth() {
  return useAuthStore()
}
