<template>
  <section class="auth-page">
    <div class="text-center mb-4">
      <h2 class="fw-semibold mb-2">Crea una cuenta</h2>
      <p class="text-muted mb-0">Ingresa tus datos para registrarte</p>
    </div>
    <div class="card shadow-sm auth-card">
      <div class="card-body p-4">
        <form @submit.prevent="submit" class="d-grid gap-3">
          <div>
            <label class="form-label fw-semibold">Nombre</label>
            <input
              v-model="name"
              type="text"
              class="form-control form-control-lg"
              placeholder="Nombre completo"
              required
              autocomplete="name"
            />
          </div>
          <div>
            <label class="form-label fw-semibold">Correo electrónico</label>
            <input
              v-model="email"
              type="email"
              class="form-control form-control-lg"
              placeholder="email@example.com"
              required
              autocomplete="email"
            />
          </div>
          <div>
            <label class="form-label fw-semibold">Contraseña</label>
            <input
              v-model="password"
              type="password"
              class="form-control form-control-lg"
              placeholder="Contraseña"
              required
              autocomplete="new-password"
            />
          </div>
          <div>
            <label class="form-label fw-semibold">Confirmar contraseña</label>
            <input
              v-model="password_confirmation"
              type="password"
              class="form-control form-control-lg"
              placeholder="Confirma tu contraseña"
              required
              autocomplete="new-password"
            />
          </div>
          <div v-if="error" class="alert alert-danger py-2 mb-0">{{ error }}</div>
          <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Crear cuenta
          </button>
          <p class="text-center text-muted mb-0">
            ¿Ya tienes cuenta?
            <RouterLink to="/login">Inicia sesión</RouterLink>
          </p>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'

const router = useRouter()
const auth = useAuth()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const loading = ref(false)
const error = ref('')

const submit = async () => {
  loading.value = true
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value, password_confirmation.value)
    router.push('/dashboard')
  } catch (e) {
    error.value = 'No se pudo registrar'
  } finally {
    loading.value = false
  }
}
</script>
