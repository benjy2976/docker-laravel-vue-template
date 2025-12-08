<script setup>
import { onMounted, ref } from 'vue'
import { usePermissionStore } from '@stores/admin/permissions'
import Filter from '@/modules/admin/permissions/Filter.vue'
import List from '@/modules/admin/permissions/List.vue'
import Form from '@/modules/admin/permissions/Form.vue'

const permissionStore = usePermissionStore()
const formRef = ref(null)

onMounted(() => {
  permissionStore.get()
})
</script>

<template>
  <section class="p-3">
    <div class="card card-body">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h2 class="h5 mb-1">Permisos</h2>
          <p class="text-muted mb-0">Administra los permisos y las entradas de menú.</p>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary" @click="formRef?.openCreate()">Nuevo permiso</button>
        </div>
      </header>

      <Filter />
      <List class="mb-3" @edit="formRef?.openEdit($event)" />
    </div>
    <Form ref="formRef" />
  </section>
</template>
