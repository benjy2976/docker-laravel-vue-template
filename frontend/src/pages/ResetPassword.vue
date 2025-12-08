<template>
  <section class="auth-page">
    <div class="text-center mb-4">
      <h2 class="fw-semibold mb-2">Restablece tu contraseña</h2>
      <p class="text-muted mb-0">Elige una nueva contraseña para tu cuenta</p>
    </div>
    <div class="card shadow-sm auth-card">
      <div class="card-body p-4">
        <form @submit.prevent="submit" class="d-grid gap-3">
          <div>
            <label class="form-label fw-semibold">Correo electrónico</label>
            <input
              v-model="form.email"
              type="email"
              class="form-control form-control-lg"
              required
              autocomplete="email"
            />
          </div>
          <div>
            <label class="form-label fw-semibold">Contraseña</label>
            <input
              v-model="form.password"
              type="password"
              class="form-control form-control-lg"
              placeholder="Nueva contraseña"
              required
              autocomplete="new-password"
            />
          </div>
          <div>
            <label class="form-label fw-semibold">Confirmar contraseña</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="form-control form-control-lg"
              placeholder="Confirma tu contraseña"
              required
              autocomplete="new-password"
            />
          </div>
          <div v-if="message" class="alert alert-success py-2 mb-0">{{ message }}</div>
          <div v-if="error" class="alert alert-danger py-2 mb-0">{{ error }}</div>
          <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Restablecer contraseña
          </button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/axios'

const route = useRoute()
const router = useRouter()

const form = reactive({
  token                 : route.params.token || '',
  email                 : route.query.email || '',
  password              : '',
  password_confirmation : '',
})

const loading = ref(false)
const error = ref('')
const message = ref('')

onMounted(() => {
  if (route.query.email) {
    form.email = route.query.email
  }
})

const submit = async () => {
  loading.value = true
  error.value = ''
  message.value = ''
  try {
    await api.get('/sanctum/csrf-cookie')
    await api.post('/reset-password', form)
    message.value = 'Contraseña actualizada. Redirigiendo al login...'
    setTimeout(() => router.push({ name: 'login' }), 1000)
  } catch (e) {
    error.value = 'No se pudo restablecer la contraseña'
  } finally {
    loading.value = false
  }
}
</script>
