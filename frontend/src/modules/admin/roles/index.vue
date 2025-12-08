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
      <header>
        <h2
          class="h5 mb-1">Roles</h2>
        <p class="text-muted mb-0">Administra los roles y sus permisos.</p>
      </header>
      <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
        <Filter class="mb-0 flex-shrink-0" />
        <button type="button" class="btn btn-primary" @click="formRef?.openCreate()">Nuevo rol</button>
      </div>

      <List class="mb-3" @edit="formRef?.openEdit($event)" />
    </div>
    <Form ref="formRef" />
  </section>
</template>
