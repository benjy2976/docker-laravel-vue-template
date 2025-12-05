<script setup>
import { storeToRefs } from 'pinia'
import { usePermissionStore } from '@stores/admin/permissions'

const permissionStore = usePermissionStore()
const { list } = storeToRefs(permissionStore)
</script>

<template>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Label</th>
          <th scope="col">Path</th>
          <th scope="col" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="permission in list" :key="permission.id">
          <td>{{ permission.name }}</td>
          <td>{{ permission.menu_label || '—' }}</td>
          <td>{{ permission.menu_path || '—' }}</td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-primary me-1">Edit</button>
            <button type="button" class="btn btn-sm btn-outline-danger">Delete</button>
          </td>
        </tr>
        <tr v-if="!list.length">
          <td colspan="4" class="text-center text-muted">No permissions found</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
