<script setup>
import { ref } from 'vue'

const form = ref({
  name        : '',
  description : '',
  menu_label  : '',
  menu_path   : '',
  icon        : '',
})

const submit = () => {
  // TODO: implement create/edit logic
}
</script>

<template>
  <form class="card card-body" @submit.prevent="submit">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionName">Name</label>
        <input
          id="permissionName"
          v-model="form.name"
          type="text"
          class="form-control"
          placeholder="permissions.view"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionLabel">Label</label>
        <input
          id="permissionLabel"
          v-model="form.menu_label"
          type="text"
          class="form-control"
          placeholder="Permisos"
        />
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold" for="permissionDescription">Description</label>
        <textarea
          id="permissionDescription"
          v-model="form.description"
          rows="2"
          class="form-control"
          placeholder="Descripción del permiso"
        ></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionPath">Path</label>
        <input
          id="permissionPath"
          v-model="form.menu_path"
          type="text"
          class="form-control"
          placeholder="/permissions"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="permissionIcon">Icon</label>
        <input
          id="permissionIcon"
          v-model="form.icon"
          type="text"
          class="form-control"
          placeholder="bi-shield-lock"
        />
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-outline-secondary">Cancel</button>
    </div>
  </form>
</template>
