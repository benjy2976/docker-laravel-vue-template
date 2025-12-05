<script setup>
import { storeToRefs } from 'pinia'
import { useRoleStore } from '@stores/admin/roles'

const roleStore = useRoleStore()
const { list } = storeToRefs(roleStore)
</script>

<template>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th scope="col">Role</th>
          <th scope="col">Permissions</th>
          <th scope="col" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="role in list" :key="role.id">
          <td>{{ role.name }}</td>
          <td>{{ (role.permissions || []).map(p => p.name).join(', ') || '—' }}</td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-primary me-1">Edit</button>
            <button type="button" class="btn btn-sm btn-outline-danger">Delete</button>
          </td>
        </tr>
        <tr v-if="!list.length">
          <td colspan="3" class="text-center text-muted">No roles found</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
