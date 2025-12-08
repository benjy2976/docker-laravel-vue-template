<script setup>
import { storeToRefs } from 'pinia'
import { useRoleStore } from '@stores/admin/roles'

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
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th scope="col">Rol</th>
          <th scope="col">Permisos</th>
          <th scope="col" class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="role in list" :key="role.id">
          <td>{{ role.name }}</td>
          <td>{{ (role.permissions || []).map(p => p.name).join(', ') || '—' }}</td>
          <td class="text-end">
            <button
              type="button"
              class="btn btn-sm btn-outline-primary me-1"
              @click="emit('edit', role)"
            >
              Editar
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="confirmDelete(role)">Eliminar</button>
          </td>
        </tr>
        <tr v-if="!list.length">
          <td colspan="3" class="text-center text-muted">No hay roles</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
