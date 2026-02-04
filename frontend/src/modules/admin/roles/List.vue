<script setup>
import { storeToRefs } from 'pinia'
import { useRoleStore } from '@stores/admin/roles'
import { useAuth } from '@stores/auth'
import Table from '@/components/Table.vue'

const roleStore = useRoleStore()
const { list } = storeToRefs(roleStore)
const auth = useAuth()

const headers = [
  { title: 'Rol', key: 'name' },
  { title: 'Permisos', key: 'permissions', sortable: false },
]

const emit = defineEmits(['edit'])

// Confirma eliminacion del rol y actualiza el store; no retorna.
const confirmDelete = async (role) => {
  const ok = window.confirm(`¿Eliminar el rol "${role.name}"?`)
  if (!ok) return
  await roleStore.delete(role)
}
</script>

<template>
  <Table
    :items="list"
    :headers="headers"
    :default-sort="{ key: 'id', direction: 'desc' }"
    :page-length="10"
    tag="role"
  >
    <template #actions="{ selected }">
      <div class="d-flex align-items-center gap-2">
        <button
          v-if="auth.can('roles.edit')"
          type="button"
          class="btn btn-outline-primary btn-sm"
          :disabled="!selected.item"
          @click="selected.item && emit('edit', selected.item)"
        >
          Editar
        </button>
        <button
          v-if="auth.can('roles.delete')"
          type="button"
          class="btn btn-outline-danger btn-sm"
          :disabled="!selected.item"
          @click="selected.item && confirmDelete(selected.item)"
        >
          Eliminar
        </button>
      </div>
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
