<script setup>
import { RouterLink } from 'vue-router'

const props = defineProps({
  userName: { type: String, default: 'Usuario' },
  userEmail: { type: String, default: '' },
  currentPath: { type: String, default: '/' },
})

const emit = defineEmits(['settings', 'logout'])
</script>

<template>
  <aside class="flex-shrink-0 p-3 bg-body-tertiary sidebar-menu">
    <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none" href="#">
      <span class="fs-4">Sidebar</span>
    </a>
    <hr />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <RouterLink
          class="nav-link"
          :class="{ active: props.currentPath === '/dashboard' }"
          to="/dashboard"
        >
          Home
        </RouterLink>
      </li>
      <li>
        <RouterLink
          class="nav-link"
          :class="{ active: props.currentPath.startsWith('/projects') }"
          to="/projects"
        >
          Dashboard
        </RouterLink>
      </li>
      <li><a href="#" class="nav-link link-body-emphasis">Orders</a></li>
      <li><a href="#" class="nav-link link-body-emphasis">Products</a></li>
      <li><a href="#" class="nav-link link-body-emphasis">Customers</a></li>
    </ul>
    <hr />
    <div class="dropdown">
      <a
        href="#"
        class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
        data-bs-toggle="dropdown"
        aria-expanded="false"
      >
        <div class="avatar-sm me-2">{{ props.userName.charAt(0).toUpperCase() }}</div>
        <strong>{{ props.userName }}</strong>
      </a>
      <ul class="dropdown-menu text-small shadow">
        <li><button class="dropdown-item" @click="emit('settings')">Settings</button></li>
        <li><hr class="dropdown-divider" /></li>
        <li><button class="dropdown-item text-danger" @click="emit('logout')">Sign out</button></li>
        <li class="dropdown-item disabled text-wrap">
          <small class="text-muted">{{ props.userEmail }}</small>
        </li>
      </ul>
    </div>
  </aside>
</template>
