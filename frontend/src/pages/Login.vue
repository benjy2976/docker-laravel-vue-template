<template>
  <section class="auth-page">
    <div class="text-center mb-4">
      <h2 class="fw-semibold mb-2">Log in to your account</h2>
      <p class="text-muted mb-0">Enter your email and password below to log in</p>
    </div>
    <div class="card shadow-sm auth-card">
      <div class="card-body p-4">
        <form @submit.prevent="submit" class="d-grid gap-3">
          <div>
            <label class="form-label fw-semibold">Email address</label>
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
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label fw-semibold mb-0">Password</label>
              <RouterLink class="small" to="/forgot-password">Forgot password?</RouterLink>
            </div>
            <input
              v-model="password"
              type="password"
              class="form-control form-control-lg"
              placeholder="Password"
              required
              autocomplete="current-password"
            />
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" v-model="remember" />
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <div v-if="error" class="alert alert-danger py-2 mb-0">{{ error }}</div>
          <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Log in
          </button>
          <p class="text-center text-muted mb-0">
            Don't have an account?
            <RouterLink to="/register">Sign up</RouterLink>
          </p>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@stores/auth/index.js'

const router = useRouter()
const auth = useAuth()

const email = ref('admin@example.com')
const password = ref('password')
const remember = ref(false)
const loading = ref(false)
const error = ref('')

const submit = async () => {
  loading.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value, remember.value)
    router.push('/dashboard')
  } catch (e) {
    error.value = 'Credenciales inválidas'
  } finally {
    loading.value = false
  }
}
</script>
