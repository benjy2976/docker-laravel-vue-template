<script setup>
import { RouterLink } from 'vue-router'

const props = defineProps({
  userName: { type: String, default: 'User' },
})

const sections = [
  {
    title: 'Home',
    children: [
      { label: 'Overview', to: '/dashboard' },
      { label: 'Updates', to: '/dashboard/updates' },
      { label: 'Reports', to: '/dashboard/reports' },
    ],
  },
  {
    title: 'Dashboard',
    children: [
      { label: 'Overview', to: '/dashboard' },
      { label: 'Weekly', to: '/dashboard/weekly' },
      { label: 'Monthly', to: '/dashboard/monthly' },
      { label: 'Annually', to: '/dashboard/annually' },
    ],
  },
  {
    title: 'Orders',
    children: [
      { label: 'New', to: '/orders/new' },
      { label: 'Processed', to: '/orders/processed' },
      { label: 'Shipped', to: '/orders/shipped' },
      { label: 'Returned', to: '/orders/returned' },
    ],
  },
]
</script>

<template>
  <aside class="flex-shrink-0 p-3 bg-body-tertiary sidebar-menu">
    <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none" href="#">
      <span class="fs-4">Collapsible</span>
    </a>
    <hr />
    <ul class="list-unstyled ps-0">
      <li v-for="section in sections" :key="section.title" class="mb-1">
        <div class="fw-semibold d-flex align-items-center gap-1 mb-1">
          <span class="small">▾</span>
          {{ section.title }}
        </div>
        <ul class="list-unstyled ps-3 mb-3">
          <li v-for="item in section.children" :key="item.to">
            <RouterLink class="d-inline-flex text-decoration-none py-1" :to="item.to">
              {{ item.label }}
            </RouterLink>
          </li>
        </ul>
      </li>
    </ul>
    <hr />
    <div>
      <RouterLink class="d-inline-flex text-decoration-none fw-semibold" to="/account">Account</RouterLink>
    </div>
    <div class="dropdown mt-auto">
      <a
        href="#"
        class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
        data-bs-toggle="dropdown"
        aria-expanded="false"
      >
        <i class="bi bi-person-circle fs-4 me-2"></i>
        <strong>{{ props.userName || 'User' }}</strong>
      </a>
      <ul class="dropdown-menu text-small shadow">
        <li><a class="dropdown-item" href="#">New project...</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider" /></li>
        <li><a class="dropdown-item" href="#">Sign out</a></li>
      </ul>
    </div>
  </aside>
</template>
