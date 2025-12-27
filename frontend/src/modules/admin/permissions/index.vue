<script setup>
import { onMounted, ref } from 'vue'
import { usePermissionStore } from '@stores/admin/permissions'
import Filter from '@/modules/admin/permissions/Filter.vue'
import List from '@/modules/admin/permissions/List.vue'
import Form from '@/modules/admin/permissions/Form.vue'
import { useAuth } from '@stores/auth'

const permissionStore = usePermissionStore()
const auth = useAuth()
const formRef = ref(null)

onMounted(() => {
  permissionStore.get()
})
</script>

<template>
  <section class="p-3">
    <div class="card card-body">
      <header>
        <h2 class="h5 mb-1">Permisos</h2>
        <p class="text-muted mb-0">Administra los permisos y las entradas de menú.</p>
      </header>

      <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
        <Filter class="mb-0 flex-shrink-0" />
        <button
          v-if="auth.can('permissions.create')"
          type="button"
          class="btn btn-primary"
          @click="formRef?.openCreate()"
        >
          Nuevo permiso
        </button>
      </div>

      <List class="mb-3" @edit="formRef?.openEdit($event)" />
    </div>
    <Form ref="formRef" />
  </section>
</template>
