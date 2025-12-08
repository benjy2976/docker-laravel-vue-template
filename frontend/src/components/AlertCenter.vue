<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

const show = ref(false)
const container = ref(null)

const alerts = [
  {
    date    : '12 diciembre 2019',
    title   : 'Nuevo reporte mensual listo para descargar',
    message : '',
    icon    : 'bi-file-earmark-text',
    variant : 'primary',
  },
  {
    date    : '7 diciembre 2019',
    title   : 'Se han depositado $290.29 en tu cuenta',
    message : '',
    icon    : 'bi-cash-coin',
    variant : 'success',
  },
  {
    date    : '2 diciembre 2019',
    title   : 'Alerta de gasto inusual detectada',
    message : 'Revisa tus movimientos recientes.',
    icon    : 'bi-exclamation-triangle',
    variant : 'warning',
  },
]

const toggle = () => {
  show.value = !show.value
}

const handleClickOutside = (e) => {
  if (show.value && container.value && !container.value.contains(e.target)) {
    show.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div ref="container" class="position-relative">
    <button
      type="button"
      class="btn nav-link pe-3 text-white position-relative"
      aria-label="Alertas"
      @click.stop="toggle"
    >
      <i class="bi bi-bell fs-5"></i>
      <span class="badge-counter">3+</span>
    </button>

    <div
      v-if="show"
      class="alert-panel card shadow border-0"
    >
      <div class="card-header bg-dark text-white fw-semibold py-2">
        Centro de notificaciones
      </div>
      <div class="list-group list-group-flush">
        <div
          v-for="(alert, idx) in alerts"
          :key="idx"
          class="list-group-item d-flex gap-3 align-items-start"
        >
          <span :class="['badge', `bg-${alert.variant}`]" class="p-3 rounded-circle d-inline-flex align-items-center justify-content-center">
            <i :class="['bi', alert.icon]"></i>
          </span>
          <div class="flex-grow-1">
            <small class="text-muted d-block">{{ alert.date }}</small>
            <div class="fw-semibold">{{ alert.title }}</div>
            <div v-if="alert.message" class="text-muted small">{{ alert.message }}</div>
          </div>
        </div>
      </div>
      <div class="card-footer text-center py-2">
        <button type="button" class="btn btn-link text-decoration-none p-0" @click.stop="show = false">
          Ver todas las alertas
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.alert-panel {
  position: absolute;
  right: 0;
  top: 120%;
  width: 320px;
  z-index: 1200;
}
.badge-counter {
  position: absolute;
  top: 13px;
  left: 85%;
  transform: translate(-50%, -50%);
  padding: 2px 5px;
  font-size: 0.65rem;
  line-height: 1;
  border-radius: 999px;
  background-color: var(--bs-danger);
  color: #fff;
}
</style>
