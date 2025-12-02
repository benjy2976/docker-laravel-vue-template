<template>
  <section class="auth-page">
    <div class="text-center mb-4">
      <h2 class="fw-semibold mb-2">Forgot password</h2>
      <p class="text-muted mb-0">Enter your email to receive a password reset link</p>
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
          <div v-if="message" class="alert alert-success py-2 mb-0">{{ message }}</div>
          <div v-if="error" class="alert alert-danger py-2 mb-0">{{ error }}</div>
          <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Email password reset link
          </button>
          <p class="text-center text-muted mb-0">
            Or, return to <RouterLink to="/login">log in</RouterLink>
          </p>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import api from '../axios'

const email = ref('')
const loading = ref(false)
const error = ref('')
const message = ref('')

const submit = async () => {
  loading.value = true
  error.value = ''
  message.value = ''
  try {
    await api.get('/sanctum/csrf-cookie')
    await api.post('/forgot-password', { email: email.value })
    message.value = 'We have emailed your password reset link.'
  } catch (e) {
    error.value = 'No se pudo enviar el enlace'
  } finally {
    loading.value = false
  }
}
</script>
