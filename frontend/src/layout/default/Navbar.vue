<script setup>
const emit = defineEmits(['settings', 'logout'])
</script>

<template>
  <header class="navbar sticky-top bg-body-tertiary flex-md-nowrap p-0 shadow">
    <span class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6">Company name</span>
    <button
      class="navbar-toggler position-absolute d-md-none collapsed"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#sidebarMenu"
      aria-controls="sidebarMenu"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <input
      class="form-control form-control-light w-100 rounded-0 border-0"
      type="text"
      placeholder="Search"
      aria-label="Search"
    />
    <div class="navbar-nav flex-row d-none d-md-flex px-3">
      <button class="btn btn-outline-secondary btn-sm me-2" @click="emit('settings')">Settings</button>
      <button class="btn btn-danger btn-sm" @click="emit('logout')">Sign out</button>
    </div>
  </header>
</template>
