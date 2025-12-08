<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '@stores/auth'

const auth = useAuth()
const form = ref({
  current_password      : '',
  password              : '',
  password_confirmation : '',
})

const notifyUnsupported = () => window.alert('Not implemented yet')

const onSubmit = async () => {
  await auth.updatePassword(form.value)
  window.alert('Password updated')
  form.value = {
    current_password      : '',
    password              : '',
    password_confirmation : '',
  }
}
</script>

<template>
  <div class="container py-4">
    <nav class="mb-3 text-muted small">
      <span>Settings</span> / <span class="text-dark fw-semibold">Password</span>
    </nav>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="list-group">
          <RouterLink to="/settings/profile" class="list-group-item list-group-item-action">Profile</RouterLink>
          <RouterLink to="/settings/password" class="list-group-item list-group-item-action active">Password</RouterLink>
          <button type="button" class="list-group-item list-group-item-action" @click="notifyUnsupported">Two-Factor Auth</button>
        </div>
      </div>
      <div class="col-md-9">
        <section>
          <h2 class="h4">Update password</h2>
          <p class="text-muted">Ensure your account is using a long, random password to stay secure</p>
          <form class="card card-body shadow-sm border-0" @submit.prevent="onSubmit">
            <div class="mb-3">
              <label class="form-label fw-semibold" for="currentPassword">Current password</label>
              <input
                id="currentPassword"
                v-model="form.current_password"
                type="password"
                class="form-control"
                placeholder="Current password"
              />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" for="newPassword">New password</label>
              <input
                id="newPassword"
                v-model="form.password"
                type="password"
                class="form-control"
                placeholder="New password"
              />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" for="confirmPassword">Confirm password</label>
              <input
                id="confirmPassword"
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                placeholder="Confirm password"
              />
            </div>
            <button type="submit" class="btn btn-primary">Save password</button>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>
