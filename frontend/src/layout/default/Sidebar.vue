<script setup>
import { RouterLink } from "vue-router";

const props = defineProps({
  userName : { type: String, default: "User" }
});

const sections = [
  {
    title    : "Home",
    children : [
      { label: "Dashboard", to: "/dashboard" },
      { label: "Updates", to: "/dashboard/updates" },
      { label: "Reports", to: "/dashboard/reports" }
    ]
  },
  {
    title    : "Orders",
    children : [
      { label: "New", to: "/orders/new" },
      { label: "Processed", to: "/orders/processed" },
      { label: "Shipped", to: "/orders/shipped" },
      { label: "Returned", to: "/orders/returned" }
    ]
  }
];
</script>

<template>
  <div class="sidebar border border-right col-md-3 col-lg-2 py-0 pe-0 bg-body-tertiary">
    
    <div
      class="offcanvas-md offcanvas-end bg-body-tertiary"
      tabindex="-1"
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
              
        <ul class="list-unstyled ps-0">
          <li v-for="(section, i) in sections" :key="section.title" class="mb-1">
            <button
              class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
              data-bs-toggle="collapse"
              :data-bs-target="`#${section.title.toLowerCase() }-${i}-collapse`"
              aria-expanded="false"
            >
              {{ section.title }}
            </button>
            <div class="collapse" :id="`${section.title.toLowerCase() }-${i}-collapse`">
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li v-for="item in section.children" :key="item.to">
                  <RouterLink
                    class="link-body-emphasis d-inline-flex text-decoration-none rounded"
                    :to="item.to"
                  >
                    {{ item.label }}
                  </RouterLink>
                </li>
              </ul>
            </div>
          </li>
          <li>
            <div class="dropdown mt-auto">
              <a
                href="#"
                class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <strong>{{ props.userName || "User" }}</strong>
              </a>
              <ul class="dropdown-menu text-small shadow">
                <li><a class="dropdown-item" href="#">New project...</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="#">Sign out</a></li>
              </ul>
            </div>
          </li>
        </ul>
        
        
      </div>
    </div>
  </div>
</template>

<style scoped>


</style>
