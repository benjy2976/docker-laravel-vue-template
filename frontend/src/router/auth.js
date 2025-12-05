import Login from '@/pages/Login.vue'
import Register from '@/pages/Register.vue'
import ForgotPassword from '@/pages/ForgotPassword.vue'
import ResetPassword from '@/pages/ResetPassword.vue'

export const authRoutes = [
  {
    path      : '/login',
    name      : 'login',
    component : Login,
    meta      : { hideChrome: true }
  },
  { 
    path      : '/register',
    name      : 'register',
    component : Register,
    meta      : { hideChrome: true }
  },
  {
    path      : '/forgot-password',
    name      : 'forgot',
    component : ForgotPassword,
    meta      : { hideChrome: true }
  },
  { 
    path      : '/password-reset/:token',
    name      : 'password-reset',
    component : ResetPassword,
    meta      : { hideChrome: true },
    props     : true
  },
]
