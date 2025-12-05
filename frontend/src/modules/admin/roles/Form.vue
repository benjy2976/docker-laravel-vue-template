<script setup>
const submit = () => {
  // TODO: implement create/edit logic
}
</script>

<template>
  <form class="card card-body" @submit.prevent="submit">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="roleName">Name</label>
        <input id="roleName" type="text" class="form-control" placeholder="admin" />
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold" for="rolePermissions">Permissions</label>
        <select id="rolePermissions" class="form-select" multiple>
          <option>permissions.view</option>
          <option>roles.view</option>
        </select>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-outline-secondary">Cancel</button>
    </div>
  </form>
</template>
