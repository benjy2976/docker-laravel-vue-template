<script setup>
import { RouterLink } from 'vue-router'
import { computed, onMounted, reactive } from 'vue'
import { usePermissionStore } from '@/store/auth/permissions'

const props = defineProps({
  userName: { type: String, default: 'Usuario' },
  userEmail: { type: String, default: '' },
  currentPath: { type: String, default: '/' },
})

const emit = defineEmits(['settings', 'logout'])

const permissionStore = usePermissionStore()
onMounted(() => {
  permissionStore.ensureMenusLoaded()
})

const open = reactive({})

const menuTree = computed(() => {
  const all = permissionStore.items.filter(p => p.is_menu)
  const byParent = (parentId) =>
    all
      .filter(p => (p.parent_id || null) === parentId)
      .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))

  const roots = byParent(null)
  roots.forEach(r => { if (open[r.id] === undefined) open[r.id] = true })
  return roots.map(root => ({
    ...root,
    children: byParent(root.id),
  }))
})

const toggle = (id) => {
  open[id] = !open[id]
}
</script>

<template>
  <aside class="flex-shrink-0 p-3 bg-body-tertiary sidebar-menu">
    <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none" href="#">
      <span class="fs-4">Sidebar</span>
    </a>
    <hr />
    <ul class="list-unstyled ps-0">
      <li v-for="menu in menuTree" :key="menu.id" class="mb-1">
        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0" @click="toggle(menu.id)">
          {{ menu.menu_label || menu.name }}
        </button>
        <div v-show="open[menu.id]" class="collapse show">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <li v-for="child in menu.children" :key="child.id">
              <RouterLink
                class="link-body-emphasis d-inline-flex text-decoration-none rounded py-1 ps-4"
                :class="{ 'fw-semibold': props.currentPath === (child.menu_path || '') }"
                :to="child.menu_path || '#'"
              >
                {{ child.menu_label || child.name }}
              </RouterLink>
            </li>
          </ul>
        </div>
      </li>
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
