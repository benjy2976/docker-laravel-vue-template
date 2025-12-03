import { reactive, computed } from 'vue'
import api from '../axios'

const state = reactive({
  currentUser: null,
  initialized: false,
})

export function useAuth() {
  const isAuthenticated = computed(() => !!state.currentUser)

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
      password_confirmation: passwordConfirmation,
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
    login,
    register,
    logout,
    fetchUser,
  }
}
