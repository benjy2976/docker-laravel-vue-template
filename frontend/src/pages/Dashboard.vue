<template>
  <div class="d-flex flex-column gap-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="mb-3">Dashboard</h4>
        <p v-if="!auth.user">Cargando usuario...</p>
        <div v-else class="d-grid gap-2">
          <div>
            <span class="text-muted">Bienvenido,</span>
            <strong class="ms-1">{{ auth.user.name }}</strong>
          </div>
          <div class="text-muted small">Email: {{ auth.user.email }}</div>
          <div v-if="roles?.length" class="text-muted small">
            Roles: {{ roles.join(', ') }}
          </div>
          <div v-if="permissions?.length" class="text-muted small">
            Permisos: {{ permissions.join(', ') }}
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido de ejemplo del dashboard (inspirado en la demo de Bootstrap) -->
    <div class="row g-3">
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Reporte semanal</h6>
            <p class="text-muted small mb-3">Actividad de los últimos 7 días.</p>
            <div class="placeholder-glow">
              <span class="placeholder col-12 mb-2"></span>
              <span class="placeholder col-10 mb-2"></span>
              <span class="placeholder col-8"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Conversiones</h6>
            <p class="display-6 fw-semibold mb-0">1,234</p>
            <p class="text-success small mb-0">+8% vs. semana anterior</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Tareas</h6>
            <ul class="list-unstyled mb-0 small">
              <li>✔︎ Revisar métricas</li>
              <li>✔︎ Actualizar reportes</li>
              <li>□ Lanzar nueva versión</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Tabla de ejemplo</h6>
          <button class="btn btn-sm btn-outline-secondary" type="button">Exportar</button>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Título</th>
                <th scope="col">Estado</th>
                <th scope="col">Fecha</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">1</th>
                <td>Reporte mensual</td>
                <td><span class="badge text-bg-success">Listo</span></td>
                <td>02/12/2025</td>
              </tr>
              <tr>
                <th scope="row">2</th>
                <td>Integración API</td>
                <td><span class="badge text-bg-warning">En progreso</span></td>
                <td>05/12/2025</td>
              </tr>
              <tr>
                <th scope="row">3</th>
                <td>Auditoría</td>
                <td><span class="badge text-bg-secondary">Pendiente</span></td>
                <td>08/12/2025</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAuth } from '@stores/auth/index.js'

const auth = useAuth()

const roles = computed(() => auth.user?.roles?.map(r => r.name) ?? [])
const permissions = computed(() => auth.user?.permissions?.map(p => p.name) ?? [])

onMounted(async () => {
  if (!auth.user) {
    try {
      await auth.fetchUser()
    } catch (e) {
      // ignore
    }
  }
})
</script>
