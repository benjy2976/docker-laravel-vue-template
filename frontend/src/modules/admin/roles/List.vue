<script setup>
import { storeToRefs } from 'pinia'
import { useRoleStore } from '@stores/admin/roles'
import Table from '@/components/Table.vue'

const roleStore = useRoleStore()
const { list } = storeToRefs(roleStore)

const emit = defineEmits(['edit'])

const confirmDelete = async (role) => {
  const ok = window.confirm(`¿Eliminar el rol "${role.name}"?`)
  if (!ok) return
  await roleStore.delete(role)
}
</script>

<template>
  <Table :items="list" :page-length="10" tag="role">
    <template #header>
      <tr>
        <th scope="col">Rol</th>
        <th scope="col">Permisos</th>
        <th scope="col" class="text-end">Acciones</th>
      </tr>
    </template>

    <template #body="{ item }">
      <td>{{ item.name }}</td>
      <td>{{ (item.permissions || []).map(p => p.name).join(', ') || '—' }}</td>
      <td class="text-end">
        <button
          type="button"
          class="btn btn-sm btn-outline-primary me-1"
          @click="emit('edit', item)"
        >
          Editar
        </button>
        <button type="button" class="btn btn-sm btn-outline-danger" @click="confirmDelete(item)">Eliminar</button>
      </td>
    </template>

    <template #footer>
      <tr v-if="!list.length">
        <td colspan="3" class="text-center text-muted">No hay roles</td>
      </tr>
    </template>
  </Table>
</template>
