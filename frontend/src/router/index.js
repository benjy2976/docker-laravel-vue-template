import { createRouter, createWebHistory } from 'vue-router'
import { authRoutes } from './auth.js'
import { adminRoutes } from './admin/index.js'
import Dashboard from '../pages/Dashboard.vue'
import ProjectsIndex from '../pages/ProjectsIndex.vue'
import ProjectsForm from '../pages/ProjectsForm.vue'
import { useAuth } from '@stores/auth.js'

const routes = [
  ...authRoutes,
  ...adminRoutes,
  { 
    path      : '/dashboard', 
    name      : 'dashboard', 
    component : Dashboard,
    meta      : { requiresAuth: true } 
  },
  { 
    path      : '/projects', 
    name      : 'projects.index', 
    component : ProjectsIndex,
    meta      : { requiresAuth: true } 
  },
  { 
    path      : '/projects/create', 
    name      : 'projects.create',
    component : ProjectsForm,
    meta      : { requiresAuth: true }
  },
  { 
    path      : '/projects/:id/edit',
    name      : 'projects.edit',
    component : ProjectsForm,
    props     : true,
    meta      : { requiresAuth: true } 
  },
  { 
    path : '/:pathMatch(.*)*', redirect : '/login' },
]

const router = createRouter({
  history : createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuth()

  if (!auth.initialized) {
    try {
      await auth.fetchUser()
    } catch (e) {
      console.error('Error fetching user during route guard:', e)
      // ignore
    }
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated.value) {
    return next({ name: 'login' })
  }

  if (auth.isAuthenticated.value && ['login', 'register', 'forgot'].includes(to.name)) {
    return next({ name: 'dashboard' })
  }

  next()
})

export default router
