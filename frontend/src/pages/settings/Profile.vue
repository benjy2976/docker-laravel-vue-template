<script setup>
import { ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '@stores/auth'
import { useToast } from '@/shared/toast'

const auth = useAuth()
const toast = useToast()
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

const notifyUnsupported = () => toast.info('Aún no implementado')

const onSubmit = async () => {
  await auth.updateProfile(form.value)
  toast.success('Perfil actualizado')
}
</script>

<template>
  <div class="container py-4">
    <nav class="mb-3 text-muted small">
      <span>Configuración</span> / <span class="text-dark fw-semibold">Perfil</span>
    </nav>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="list-group">
          <RouterLink to="/settings/profile" class="list-group-item list-group-item-action active">Perfil</RouterLink>
          <RouterLink to="/settings/password" class="list-group-item list-group-item-action">Contraseña</RouterLink>
          <button type="button" class="list-group-item list-group-item-action" @click="notifyUnsupported">Autenticación en dos pasos</button>
        </div>
      </div>
      <div class="col-md-9">
        <section class="mb-4">
          <h2 class="h4">Información del perfil</h2>
          <p class="text-muted">Actualiza tu nombre y correo electrónico</p>
          <form class="card card-body shadow-sm border-0" @submit.prevent="onSubmit">
            <div class="mb-3">
              <label class="form-label fw-semibold" for="profileName">Nombre</label>
              <input
                id="profileName"
                type="text"
                class="form-control"
                placeholder="Tu nombre"
                v-model="form.name"
              />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" for="profileEmail">Correo electrónico</label>
              <input
                id="profileEmail"
                type="email"
                class="form-control"
                placeholder="tu@correo.com"
                v-model="form.email"
              />
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </form>
        </section>

        <section>
          <h3 class="h5 text-danger">Eliminar cuenta</h3>
          <p class="text-muted">Elimina tu cuenta y todos sus datos</p>
          <div class="alert alert-warning d-flex flex-column gap-2 mb-0">
            <div class="fw-semibold">Advertencia</div>
            <div class="text-muted">Procede con precaución, esta acción no se puede deshacer.</div>
            <div>
              <button type="button" class="btn btn-outline-danger" @click="notifyUnsupported">Eliminar cuenta</button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
