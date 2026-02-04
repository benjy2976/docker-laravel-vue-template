<script setup>
import { computed, ref, watchEffect, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@stores/auth.js'
import DefaultLayout from './layout/default/index.vue'
import TopLayout from './layout/top/index.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuth()

// Selecciona el layout segun meta.layout o fallback; retorna un id.
const layoutMode = computed(() => route.meta.layout || 'default')
// Resuelve el componente de layout segun el modo; retorna componente Vue.
const layoutComponent = computed(() => (layoutMode.value === 'top' ? TopLayout : DefaultLayout))
// Devuelve true si se debe ocultar el chrome; retorna boolean.
const hideChrome = computed(() => route.meta.hideChrome)
// Devuelve el nombre visible del usuario; retorna string.
const userName = computed(() => auth.user?.name ?? 'Usuario')
// Devuelve el email visible del usuario; retorna string.
const userEmail = computed(() => auth.user?.email ?? '')
// Devuelve el path actual de la ruta; retorna string.
const currentPath = computed(() => route.path)

// Navega a la pantalla de configuracion; no retorna.
const goSettings = () => {
  router.push({ name: 'settings.profile' })
}
// Cierra sesion y redirige al login; no retorna.
const handleLogout = async () => {
  await auth.logout()
  router.push({ name: 'login' })
}

// Tema (light/dark/auto)
const theme = ref(localStorage.getItem('theme') || 'auto')
// Aplica el tema segun preferencia; no retorna.
const applyTheme = (value) => {
  const resolved =
    value === 'auto'
      ? window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light'
      : value
  document.documentElement.setAttribute('data-bs-theme', resolved)
}
watchEffect(() => {
  localStorage.setItem('theme', theme.value)
  applyTheme(theme.value)
})
onMounted(() => applyTheme(theme.value))
</script>

<template>
  <div class="app-shell">
    <RouterView v-if="hideChrome" />
    <component
      :is="layoutComponent"
      v-else
      :user-name="userName"
      :user-email="userEmail"
      :current-path="currentPath"
      :theme="theme"
      @change-theme="theme = $event"
      @settings="goSettings"
      @logout="handleLogout"
    >
      <RouterView />
    </component>
  </div>
</template>
