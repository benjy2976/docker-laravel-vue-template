<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { Dropdown } from 'bootstrap'
import AlertCenter from '@/components/AlertCenter.vue'
import { useAuth } from '@stores/auth'

const emit = defineEmits(['settings', 'logout'])
const profileBtn = ref(null)
let profileDropdown = null
const auth = useAuth()
const appName = import.meta.env.VITE_APP_NAME || 'Company name'

// Devuelve las secciones del menu segun permisos.
const menuSections = computed(() => auth.menuSections || [])
// Devuelve true si la seccion tiene hijos navegables.
const hasChildren = (section) => Array.isArray(section?.children) && section.children.length > 0

// Inicializa el dropdown de perfil.
onMounted(() => {
  if (profileBtn.value) {
    profileDropdown = new Dropdown(profileBtn.value, { autoClose: 'outside' })
  }
})

// Limpia el dropdown de perfil.
onBeforeUnmount(() => {
  profileDropdown?.dispose()
  profileDropdown = null
})
</script>

<template>
  <header class="navbar navbar-dark navbar-expand-lg sticky-top bg-dark shadow">
    <div class="container-fluid px-3">
      <RouterLink to="/dashboard" class="text-decoration-none fw-semibold d-flex align-items-center gap-2">
        <i class="bi bi-grid-1x2-fill text-warning fs-4"></i>
        <span class="fs-6 text-white">{{ appName }}</span>
      </RouterLink>
      <button
        class="navbar-toggler ms-auto ms-lg-3"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#topNavbarMenu"
        aria-controls="topNavbarMenu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse flex-grow-1 justify-content-center" id="topNavbarMenu">
        <ul class="navbar-nav gap-1 mb-2 mb-lg-0">
          <li
            v-for="section in menuSections"
            :key="section.title"
            class="nav-item"
            :class="{ dropdown: hasChildren(section), 'nav-hover-dropdown': hasChildren(section) }"
          >
            <template v-if="hasChildren(section)">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i v-if="section.icon" :class="['bi', section.icon, 'me-1']" aria-hidden="true"></i>
                {{ section.title }}
              </a>
              <ul class="dropdown-menu">
                <li v-for="child in section.children" :key="child.to">
                  <RouterLink class="dropdown-item" :to="child.to" active-class="active">
                    <i v-if="child.icon" :class="['bi', child.icon, 'me-2']" aria-hidden="true"></i>
                    {{ child.label }}
                  </RouterLink>
                </li>
              </ul>
            </template>
            <template v-else>
              <RouterLink class="nav-link" :to="section.to" active-class="active">
                <i v-if="section.icon" :class="['bi', section.icon, 'me-1']" aria-hidden="true"></i>
                {{ section.title }}
              </RouterLink>
            </template>
          </li>
        </ul>
      </div>

      <ul class="navbar-nav flex-row align-items-center gap-2 ms-auto">
        <li class="nav-item">
          <AlertCenter />
        </li>
        <li class="nav-item dropdown profile-dropdown">
          <button
            ref="profileBtn"
            class="btn btn-outline-secondary btn-sm dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-label="Perfil"
          >
            <i class="bi bi-person-circle me-1"></i>
            {{ auth.user?.name || 'Usuario' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item d-flex align-items-center gap-2" href="#" @click.stop.prevent="emit('settings')">
                <i class="bi bi-gear" aria-hidden="true"></i>
                <span>Configuración</span>
              </a>
            </li>
            <li><hr class="dropdown-divider" /></li>
            <li>
              <a class="dropdown-item d-flex align-items-center gap-2" href="#" @click.stop.prevent="emit('logout')">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span>Cerrar sesión</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </header>
</template>

<style scoped>
.profile-dropdown {
  position: relative;
}
.profile-dropdown .dropdown-menu {
  position: absolute;
  inset: auto 0 auto auto;
  transform: none !important;
  margin-top: .5rem;
  z-index: 1080;
}

@media (min-width: 992px) {
  .nav-hover-dropdown:hover > .dropdown-menu {
    display: block;
  }
}
</style>
