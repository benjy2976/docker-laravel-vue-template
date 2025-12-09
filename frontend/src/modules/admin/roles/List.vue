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
    <template #actions="{ selected }">
      <div class="d-flex align-items-center gap-2">
        <button
          type="button"
          class="btn btn-outline-primary btn-sm"
          :disabled="!selected.item"
          @click="selected.item && emit('edit', selected.item)"
        >
          Editar
        </button>
        <button
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
        <th scope="col">Rol</th>
        <th scope="col">Permisos</th>
      </tr>
    </template>

    <template #body="{ item }">
      <td>{{ item.name }}</td>
      <td>{{ (item.permissions || []).map(p => p.name).join(', ') || '—' }}</td>
    </template>

    <template #footer>
      <tr v-if="!list.length">
        <td colspan="2" class="text-center text-muted">No hay roles</td>
      </tr>
    </template>
  </Table>
</template>
