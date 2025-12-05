<script setup>
import { onMounted } from 'vue'
import { usePermissionStore } from '@stores/admin/permissions'
import Filter from '@/modules/admin/permissions/Filter.vue'
import List from '@/modules/admin/permissions/List.vue'
import Form from '@/modules/admin/permissions/Form.vue'

const permissionStore = usePermissionStore()

onMounted(() => {
  permissionStore.get()
})
</script>

<template>
  <section class="p-3">
    <div class="card card-body">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h2 class="h5 mb-1">Permissions</h2>
          <p class="text-muted mb-0">Manage permissions and menu entries.</p>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary">New Permission</button>
        </div>
      </header>

      <Filter />
      <List class="mb-3" />
    </div>
    <Form />
  </section>
</template>
