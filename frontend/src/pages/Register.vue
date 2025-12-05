<template>
  <section class="auth-page">
    <div class="text-center mb-4">
      <h2 class="fw-semibold mb-2">Create an account</h2>
      <p class="text-muted mb-0">Enter your details below to create your account</p>
    </div>
    <div class="card shadow-sm auth-card">
      <div class="card-body p-4">
        <form @submit.prevent="submit" class="d-grid gap-3">
          <div>
            <label class="form-label fw-semibold">Name</label>
            <input
              v-model="name"
              type="text"
              class="form-control form-control-lg"
              placeholder="Full name"
              required
              autocomplete="name"
            />
          </div>
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
            <label class="form-label fw-semibold">Password</label>
            <input
              v-model="password"
              type="password"
              class="form-control form-control-lg"
              placeholder="Password"
              required
              autocomplete="new-password"
            />
          </div>
          <div>
            <label class="form-label fw-semibold">Confirm password</label>
            <input
              v-model="password_confirmation"
              type="password"
              class="form-control form-control-lg"
              placeholder="Confirm password"
              required
              autocomplete="new-password"
            />
          </div>
          <div v-if="error" class="alert alert-danger py-2 mb-0">{{ error }}</div>
          <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            Create account
          </button>
          <p class="text-center text-muted mb-0">
            Already have an account?
            <RouterLink to="/login">Log in</RouterLink>
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
