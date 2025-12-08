<script setup>
import { onMounted, ref } from 'vue'
import { useRoleStore } from '@stores/admin/roles'
import Filter from '@/modules/admin/roles/Filter.vue'
import List from '@/modules/admin/roles/List.vue'
import Form from '@/modules/admin/roles/Form.vue'

const roleStore = useRoleStore()
const formRef = ref(null)

onMounted(() => {
  roleStore.get()
})
</script>

<template>
  <section class="p-3">
    <div class="card card-body">
      <header class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h2 class="h5 mb-1">Roles</h2>
          <p class="text-muted mb-0">Administra los roles y sus permisos.</p>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-primary" @click="formRef?.openCreate()">Nuevo rol</button>
        </div>
      </header>

      <Filter />
      <List class="mb-3" @edit="formRef?.openEdit($event)" />
    </div>
    <Form ref="formRef" />
  </section>
</template>
