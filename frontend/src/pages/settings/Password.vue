<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '@stores/auth'
import { useToast } from '@/shared/toast'

const auth = useAuth()
const toast = useToast()
const form = ref({
  current_password      : '',
  password              : '',
  password_confirmation : '',
})
const errors = ref({})

const notifyUnsupported = () => toast.info('Aún no implementado')

const onSubmit = async () => {
  errors.value = {}
  try {
    await auth.updatePassword(form.value)
    toast.success('Contraseña actualizada')
    form.value = {
      current_password      : '',
      password              : '',
      password_confirmation : '',
    }
  } catch (err) {
    const data = err?.response?.data
    if (err?.response?.status === 422 && data?.errors) {
      errors.value = data.errors
    } else {
      toast.danger('No se pudo actualizar la contraseña')
    }
  }
}
</script>

<template>
  <div class="container py-4">
    <nav class="mb-3 text-muted small">
      <span>Configuración</span> / <span class="text-dark fw-semibold">Contraseña</span>
    </nav>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="list-group">
          <RouterLink to="/settings/profile" class="list-group-item list-group-item-action">Perfil</RouterLink>
          <RouterLink to="/settings/password" class="list-group-item list-group-item-action active">Contraseña</RouterLink>
          <button type="button" class="list-group-item list-group-item-action" @click="notifyUnsupported">Autenticación en dos pasos</button>
        </div>
      </div>
      <div class="col-md-9">
        <section>
          <h2 class="h4">Actualizar contraseña</h2>
          <p class="text-muted">Usa una contraseña larga y segura para proteger tu cuenta</p>
          <form class="card card-body shadow-sm border-0" @submit.prevent="onSubmit">
            <div class="mb-3">
              <label class="form-label fw-semibold" for="currentPassword">Contraseña actual</label>
              <input
                id="currentPassword"
                v-model="form.current_password"
                type="password"
                class="form-control"
                :class="{ 'is-invalid': errors.current_password }"
                placeholder="Contraseña actual"
              />
              <div v-if="errors.current_password" class="text-danger small mt-1">
                {{ errors.current_password[0] }}
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" for="newPassword">Nueva contraseña</label>
              <input
                id="newPassword"
                v-model="form.password"
                type="password"
                class="form-control"
                :class="{ 'is-invalid': errors.password }"
                placeholder="Nueva contraseña"
              />
              <div v-if="errors.password" class="text-danger small mt-1">
                {{ errors.password[0] }}
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" for="confirmPassword">Confirmar contraseña</label>
              <input
                id="confirmPassword"
                v-model="form.password_confirmation"
                type="password"
                class="form-control"
                :class="{ 'is-invalid': errors.password_confirmation }"
                placeholder="Confirmar contraseña"
              />
              <div v-if="errors.password_confirmation" class="text-danger small mt-1">
                {{ errors.password_confirmation[0] }}
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar contraseña</button>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>
