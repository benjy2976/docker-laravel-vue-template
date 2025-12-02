<template>
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Dashboard</h4>
      <p v-if="!auth.user">Cargando usuario...</p>
      <div v-else class="d-grid gap-2">
        <div>
          <span class="text-muted">Bienvenido,</span>
          <strong class="ms-1">{{ auth.user.name }}</strong>
        </div>
        <div class="text-muted small">Email: {{ auth.user.email }}</div>
        <div v-if="roles?.length" class="text-muted small">
          Roles: {{ roles.join(', ') }}
        </div>
        <div v-if="permissions?.length" class="text-muted small">
          Permisos: {{ permissions.join(', ') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAuth } from '../stores/auth'

const auth = useAuth()

const roles = computed(() => auth.user?.roles?.map(r => r.name) ?? [])
const permissions = computed(() => auth.user?.permissions?.map(p => p.name) ?? [])

onMounted(async () => {
  if (!auth.user) {
    try {
      await auth.fetchUser()
    } catch (e) {
      // ignore
    }
  }
})
</script>
