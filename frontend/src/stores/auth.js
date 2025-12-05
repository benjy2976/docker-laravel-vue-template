import { reactive, computed } from 'vue'
import api from '@/axios'

const state = reactive({
  currentUser : null,
  initialized : false,
})

export function useAuth() {
  const isAuthenticated = computed(() => !!state.currentUser)
  const menuSections = computed(() => {
    const perms = state.currentUser?.permissions || []
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
  })

  const fetchUser = async () => {
    try {
      const { data } = await api.get('/api/user')
      state.currentUser = data
    } finally {
      state.initialized = true
    }
  }

  const login = async (email, password, remember = false) => {
    await api.get('/sanctum/csrf-cookie')
    await api.post('/login', { email, password, remember })
    await fetchUser()
  }

  const register = async (name, email, password, passwordConfirmation) => {
    await api.get('/sanctum/csrf-cookie')
    await api.post('/register', {
      name,
      email,
      password,
      password_confirmation : passwordConfirmation,
    })
    await fetchUser()
  }

  const logout = async () => {
    await api.post('/logout')
    state.currentUser = null
    state.initialized = true
  }

  return {
    get user() {
      return state.currentUser
    },
    isAuthenticated,
    get initialized() {
      return state.initialized
    },
    get menuSections() {
      return menuSections.value
    },
    login,
    register,
    logout,
    fetchUser,
  }
}
