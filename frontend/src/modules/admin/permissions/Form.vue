<script setup>
const submit = () => {
  // TODO: implement create/edit logic
}
</script>

<template>
  <form class="card card-body" @submit.prevent="submit">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionName">Name</label>
        <input id="permissionName" type="text" class="form-control" placeholder="permissions.view" />
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionLabel">Label</label>
        <input id="permissionLabel" type="text" class="form-control" placeholder="Permisos" />
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionPath">Path</label>
        <input id="permissionPath" type="text" class="form-control" placeholder="/permissions" />
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionIcon">Icon</label>
        <input id="permissionIcon" type="text" class="form-control" placeholder="bi-shield-lock" />
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-outline-secondary">Cancel</button>
    </div>
  </form>
</template>
