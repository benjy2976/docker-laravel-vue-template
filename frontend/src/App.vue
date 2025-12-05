<script setup>
import { computed, ref, watchEffect, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@stores/auth/index.js'
import DefaultLayout from './layout/default/index.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuth()

const hideChrome = computed(() => route.meta.hideChrome)
const userName = computed(() => auth.user?.name ?? 'Usuario')
const userEmail = computed(() => auth.user?.email ?? '')
const currentPath = computed(() => route.path)

const goSettings = () => {
  alert('Settings no implementado aún')
}
const handleLogout = async () => {
  await auth.logout()
  router.push({ name: 'login' })
}

// Tema (light/dark/auto)
const theme = ref(localStorage.getItem('theme') || 'auto')
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
    <DefaultLayout
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
    </DefaultLayout>
  </div>
</template>
