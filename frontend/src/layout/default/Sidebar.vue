<script setup>
import { computed } from "vue";
import { RouterLink, useRoute } from "vue-router";
import { useAuth } from "@stores/auth.js";

const props = defineProps({
  userName : { type: String, default: "User" }
});

const emit = defineEmits(['settings', 'logout'])

const baseSections = [
  {
    title    : "Home",
    icon     : "bi-house-door",
    children : [
      { label: "Dashboard", to: "/dashboard" },
      { label: "Updates", to: "/dashboard/updates" },
      { label: "Reports", to: "/dashboard/reports" }
    ]
  },
  {
    title    : "Orders",
    icon     : "bi-bag-check",
    children : [
      { label: "New", to: "/orders/new" },
      { label: "Processed", to: "/orders/processed" },
      { label: "Shipped", to: "/orders/shipped" },
      { label: "Returned", to: "/orders/returned" }
    ]
  }
];

const auth = useAuth();
const route = useRoute();
const sections = computed(() => [...baseSections, ...(auth.menuSections || [])]);

const isSectionOpen = (section) => {
  const children = section.children || [];
  return children.some((child) => route.path.startsWith(child.to));
};
</script>

<template>
  <div class="sidebar border border-right col-md-3 col-lg-2 py-0">
    
    <div
      class="offcanvas-md offcanvas-end"
      tabindex="-1"
      data-bs-scroll="true"
      id="sidebarMenu"
      aria-labelledby="sidebarMenuLabel"
    >
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="offcanvas"
          data-bs-target="#sidebarMenu"
          aria-label="Close"
        ></button>
      </div>
      <div class="offcanvas-body p-0">
              
        <ul class="list-unstyled ps-0 w-100">
          <li v-for="(section, i) in sections" :key="section.title" class="mb-1">
            <button
              class="btn btn-toggle d-inline-flex align-items-center rounded border-0"
              :class="{ collapsed: !isSectionOpen(section) }"
              data-bs-toggle="collapse"
              :data-bs-target="`#${section.title.toLowerCase() }-${i}-collapse`"
              :aria-expanded="isSectionOpen(section)"
            >
              <i :class="['bi', section.icon, 'me-2']" aria-hidden="true"></i>
              {{ section.title }}
            </button>
            <div
              :class="['collapse', { show: isSectionOpen(section) }]"
              :id="`${section.title.toLowerCase() }-${i}-collapse`"
            >
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li v-for="item in section.children" :key="item.to">
                  <RouterLink
                    class="link-body-emphasis d-inline-flex text-decoration-none rounded"
                    :to="item.to"
                  >
                    <i v-if="item.icon" :class="['bi', item.icon, 'me-2']" aria-hidden="true"></i>
                    {{ item.label }}
                  </RouterLink>
                </li>
              </ul>
            </div>
          </li>
          <hr class="my-0"/>
          <li class="mb-1">
            
            <button
              class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
              data-bs-toggle="collapse"
              :data-bs-target="`#user-collapse`"
              aria-expanded="false"
            >
              <i class="bi bi-person-circle fs-4 me-2"></i>
              {{ props.userName || "User" }}
            </button>
            
            <div class="collapse" id="user-collapse">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li>
                  <a class="link-body-emphasis d-inline-flex text-decoration-none rounded" href="#" @click.stop.prevent="emit('settings')">
                    Configuración
                  </a>
                </li>
                <li>
                  <a class="link-body-emphasis d-inline-flex text-decoration-none rounded" href="#" @click.stop.prevent="emit('logout')">
                    Cerrar Session
                  </a>
                </li>
              </ul>
            </div>
            
          </li>
        </ul>
        
        
      </div>
    </div>
  </div>
</template>

<style scoped>
.btn-toggle-nav a {
    padding: .1875rem .5rem;
    margin-top: .125rem;
    margin-left: 1.25rem;
}
.btn-toggle {
    padding: .25rem .25rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    background-color: transparent;
}

.btn-toggle-nav a.router-link-active,
.btn-toggle-nav a.router-link-exact-active {
    font-weight: 600;
    color: var(--bs-primary) !important;
}
.btn-toggle::before {
    order: 2;
    margin-left: auto;
}

.sidebar .offcanvas-body {
    overflow: visible;
}
.btn-toggle:hover,
.btn-toggle:focus {
  color: rgba(var(--bs-emphasis-color-rgb), .85);
  background-color: var(--bs-tertiary-bg);
}
.btn-toggle-nav a:hover,
.btn-toggle-nav a:focus {
  background-color: var(--bs-tertiary-bg);
}

@media (min-width: 768px) {
  .sidebar {
      position: sticky;
      top: 51px; /* altura aproximada del navbar */
      height: calc(100vh - 51px);
      overflow-y: auto;
  }
}

</style>
