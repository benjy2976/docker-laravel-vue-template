<script setup>
import { storeToRefs } from 'pinia'
import { usePermissionStore } from '@stores/admin/permissions'

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
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th scope="col">Nombre</th>
          <th scope="col">Descripción</th>
          <th scope="col">Etiqueta</th>
          <th scope="col">Ruta</th>
          <th scope="col" class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="permission in list" :key="permission.id">
          <td>{{ permission.name }}</td>
          <td>{{ permission.description || '—' }}</td>
          <td>{{ permission.menu_label || '—' }}</td>
          <td>{{ permission.menu_path || '—' }}</td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-primary me-1" @click="emit('edit', permission)">Editar</button>
            <button type="button" class="btn btn-sm btn-outline-danger" @click="confirmDelete(permission)">Eliminar</button>
          </td>
        </tr>
        <tr v-if="!list.length">
          <td colspan="4" class="text-center text-muted">No hay permisos</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
