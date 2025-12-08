<script setup>
import { ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '@stores/auth'

const auth = useAuth()
const form = ref({
  name  : '',
  email : '',
})

watch(
  () => auth.user,
  (user) => {
    if (user) {
      form.value.name = user.name || ''
      form.value.email = user.email || ''
    }
  },
  { immediate: true }
)

const notifyUnsupported = () => window.alert('Not implemented yet')

const onSubmit = async () => {
  await auth.updateProfile(form.value)
  window.alert('Profile updated')
}
</script>

<template>
  <div class="container py-4">
    <nav class="mb-3 text-muted small">
      <span>Settings</span> / <span class="text-dark fw-semibold">Profile</span>
    </nav>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="list-group">
          <RouterLink to="/settings/profile" class="list-group-item list-group-item-action active">Profile</RouterLink>
          <RouterLink to="/settings/password" class="list-group-item list-group-item-action">Password</RouterLink>
          <button type="button" class="list-group-item list-group-item-action" @click="notifyUnsupported">Two-Factor Auth</button>
        </div>
      </div>
      <div class="col-md-9">
        <section class="mb-4">
          <h2 class="h4">Profile information</h2>
          <p class="text-muted">Update your name and email address</p>
          <form class="card card-body shadow-sm border-0" @submit.prevent="onSubmit">
            <div class="mb-3">
              <label class="form-label fw-semibold" for="profileName">Name</label>
              <input
                id="profileName"
                type="text"
                class="form-control"
                placeholder="Your name"
                v-model="form.name"
              />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" for="profileEmail">Email address</label>
              <input
                id="profileEmail"
                type="email"
                class="form-control"
                placeholder="you@example.com"
                v-model="form.email"
              />
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
          </form>
        </section>

        <section>
          <h3 class="h5 text-danger">Delete account</h3>
          <p class="text-muted">Delete your account and all of its resources</p>
          <div class="alert alert-warning d-flex flex-column gap-2 mb-0">
            <div class="fw-semibold">Warning</div>
            <div class="text-muted">Please proceed with caution, this cannot be undone.</div>
            <div>
              <button type="button" class="btn btn-outline-danger" @click="notifyUnsupported">Delete account</button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
