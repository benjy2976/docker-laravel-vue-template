<script setup>
const emit = defineEmits(['settings', 'logout'])
</script>

<template>
  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <div class="d-flex align-items-center">
      <ul class="navbar-nav flex-row d-md-none mb-0">
        <button
          class="nav-link px-3 text-white"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#sidebarMenu"
          aria-controls="sidebarMenu"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
      </ul>
      <a class="navbar-brand me-0 px-2 fs-6 text-white" href="#">Company name</a>
    </div>
    <div class="navbar-nav flex-row d-none d-md-flex px-3">
      <button class="btn btn-outline-secondary btn-sm me-2" @click="emit('settings')">Settings</button>
      <button class="btn btn-danger btn-sm" @click="emit('logout')">Sign out</button>
    </div>
    <ul class="navbar-nav flex-row d-md-none">
      <li class="nav-item text-nowrap">
        <button
          class="nav-link px-3 text-white d-flex align-items-center"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSearch"
          aria-controls="navbarSearch"
          aria-expanded="false"
          aria-label="Toggle search"
        >
          <i class="bi bi-search fs-5"></i>
        </button>
      </li>
    </ul>
    <ul class="navbar-nav flex-row ms-auto align-items-center gap-2">
      <li class="nav-item">
        <button class="btn nav-link pe-3 text-white position-relative" type="button" aria-label="Notificaciones">
          <i class="bi bi-bell fs-5"></i>
          <span class="badge-counter">3+</span>
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link pe-3 text-white position-relative" type="button" aria-label="Mensajes">
          <i class="bi bi-envelope fs-5"></i>
          <span class="badge-counter">7</span>
        </button>
      </li>
      <li class="nav-item dropdown profile-dropdown">
        <button
          class="nav-link pe-3 text-white"
          type="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
          aria-label="Perfil"
        >
          <i class="bi bi-person-circle fs-4"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">New project...</a></li>
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li><a class="dropdown-item" href="#">Sign out</a></li>
        </ul>
      </li>
    </ul>
    <div id="navbarSearch" class="navbar-search w-100 collapse">
      <input
        class="form-control w-100 rounded-0 border-0"
        type="text"
        placeholder="Search"
        aria-label="Search"
      />
    </div>
  </header>
</template>
<style scoped>
.navbar-brand {
  padding-top: .75rem;
  padding-bottom: .75rem;
  background-color: rgba(0, 0, 0, .25);
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
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
</style>
