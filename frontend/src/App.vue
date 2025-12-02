<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const hideChrome = computed(() => route.meta.hideChrome)
</script>

<template>
  <div class="app-shell">
    <header v-if="!hideChrome" class="shadow-sm bg-white">
      <div class="container-fluid px-4 py-2 d-flex align-items-center justify-content-between">
        <RouterLink class="navbar-brand fw-semibold" to="/dashboard">App</RouterLink>
        <nav class="d-none d-md-flex gap-3">
          <RouterLink class="nav-link px-0" to="/dashboard">Dashboard</RouterLink>
          <RouterLink class="nav-link px-0" to="/projects">Projects</RouterLink>
          <RouterLink class="nav-link px-0" to="/login">Login</RouterLink>
          <RouterLink class="nav-link px-0" to="/register">Register</RouterLink>
        </nav>
        <div class="d-md-none">
          <button
            class="btn btn-outline-secondary btn-sm"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileNav"
            aria-controls="mobileNav"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
    </header>

    <div :class="hideChrome ? 'px-0' : 'container-fluid px-4 py-4'">
      <div :class="hideChrome ? '' : 'row'">
        <aside v-if="!hideChrome" class="col-lg-2 d-none d-lg-block">
          <div class="list-group small shadow-sm">
            <span class="list-group-item active">Menú</span>
            <RouterLink class="list-group-item list-group-item-action" to="/dashboard">Dashboard</RouterLink>
            <RouterLink class="list-group-item list-group-item-action" to="/projects">Projects</RouterLink>
            <RouterLink class="list-group-item list-group-item-action" to="/projects/create">New Project</RouterLink>
            <RouterLink class="list-group-item list-group-item-action" to="/login">Login</RouterLink>
            <RouterLink class="list-group-item list-group-item-action" to="/register">Register</RouterLink>
          </div>
        </aside>
        <main :class="hideChrome ? '' : 'col-lg-10'">
          <RouterView />
        </main>
      </div>
    </div>

    <div v-if="!hideChrome" class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileNavLabel">Menú</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="list-group">
          <RouterLink class="list-group-item list-group-item-action" to="/dashboard" data-bs-dismiss="offcanvas">Dashboard</RouterLink>
          <RouterLink class="list-group-item list-group-item-action" to="/projects" data-bs-dismiss="offcanvas">Projects</RouterLink>
          <RouterLink class="list-group-item list-group-item-action" to="/projects/create" data-bs-dismiss="offcanvas">New Project</RouterLink>
          <RouterLink class="list-group-item list-group-item-action" to="/login" data-bs-dismiss="offcanvas">Login</RouterLink>
          <RouterLink class="list-group-item list-group-item-action" to="/register" data-bs-dismiss="offcanvas">Register</RouterLink>
        </ul>
      </div>
    </div>
  </div>
</template>
