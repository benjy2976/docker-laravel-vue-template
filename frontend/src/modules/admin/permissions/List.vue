<script setup>
import { storeToRefs } from 'pinia'
import { usePermissionStore } from '@stores/admin/permissions'
import { useAuth } from '@stores/auth'
import Table from '@/components/Table.vue'

const permissionStore = usePermissionStore()
const { list } = storeToRefs(permissionStore)
const auth = useAuth()

const emit = defineEmits(['edit'])

const confirmDelete = async (permission) => {
  const ok = window.confirm(`¿Eliminar el permiso "${permission.name}"?`)
  if (!ok) return
  await permissionStore.delete(permission)
}
</script>

<template>
  <Table :items="list" :page-length="10" tag="perm">
    <template #actions="{ selected }">
      <div class="d-flex align-items-center gap-2">
        <button
          v-if="auth.can('permissions.edit')"
          type="button"
          class="btn btn-outline-primary btn-sm"
          :disabled="!selected.item"
          @click="selected.item && emit('edit', selected.item)"
        >
          Editar
        </button>
        <button
          v-if="auth.can('permissions.delete')"
          type="button"
          class="btn btn-outline-danger btn-sm"
          :disabled="!selected.item"
          @click="selected.item && confirmDelete(selected.item)"
        >
          Eliminar
        </button>
      </div>
    </template>

    <template #header>
      <tr>
        <th scope="col">Nombre</th>
        <th scope="col">Descripción</th>
        <th scope="col">Etiqueta</th>
        <th scope="col">Ruta</th>
      </tr>
    </template>

    <template #body="{ item }">
      <td>{{ item.name }}</td>
      <td>{{ item.description || '—' }}</td>
      <td>{{ item.menu_label || '—' }}</td>
      <td>{{ item.menu_path || '—' }}</td>
    </template>

    <template #footer>
      <tr v-if="!list.length">
        <td colspan="4" class="text-center text-muted">No hay permisos</td>
      </tr>
    </template>
  </Table>
</template>
