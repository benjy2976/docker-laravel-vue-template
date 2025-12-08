<script setup>
import { storeToRefs } from 'pinia'
import { usePermissionStore } from '@stores/admin/permissions'
import Table from '@/components/Table.vue'

const permissionStore = usePermissionStore()
const { list } = storeToRefs(permissionStore)

const emit = defineEmits(['edit'])

const confirmDelete = async (permission) => {
  const ok = window.confirm(`¿Eliminar el permiso "${permission.name}"?`)
  if (!ok) return
  await permissionStore.delete(permission)
}
</script>

<template>
  <Table :items="list" :page-length="10" tag="perm">
    <template #header>
      <tr>
        <th scope="col">Nombre</th>
        <th scope="col">Descripción</th>
        <th scope="col">Etiqueta</th>
        <th scope="col">Ruta</th>
        <th scope="col" class="text-end">Acciones</th>
      </tr>
    </template>

    <template #body="{ item, index }">
      <td>{{ item.name }}</td>
      <td>{{ item.description || '—' }}</td>
      <td>{{ item.menu_label || '—' }}</td>
      <td>{{ item.menu_path || '—' }}</td>
      <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-primary me-1" @click="emit('edit', item)">Editar</button>
        <button type="button" class="btn btn-sm btn-outline-danger" @click="confirmDelete(item)">Eliminar</button>
      </td>
    </template>

    <template #footer>
      <tr v-if="!list.length">
        <td colspan="5" class="text-center text-muted">No hay permisos</td>
      </tr>
    </template>
  </Table>
</template>
