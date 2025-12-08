<script setup>
import { ref, onMounted, onBeforeUnmount, defineExpose } from 'vue'
import { Modal } from 'bootstrap'
import { Permission } from '@core/admin/permission'
import { usePermissionStore } from '@stores/admin/permissions'

const modalEl = ref(null)
let modalInstance = null
const permissionStore = usePermissionStore()
let hideListener = null

const defaultPermission = () => Permission.getDefault()
const form = ref(defaultPermission())
const mode = ref('create') // 'create' | 'edit'

const submit = async () => {
  if (mode.value === 'edit') {
    await permissionStore.update(form.value)
  } else {
    await permissionStore.create(form.value)
  }
  close()
}

const openCreate = () => {
  mode.value = 'create'
  form.value = defaultPermission()
  modalInstance?.show()
}

const openEdit = (permission) => {
  mode.value = 'edit'
  form.value = { ...defaultPermission(), ...permission }
  modalInstance?.show()
}

const close = () => {
  modalInstance?.hide()
}

defineExpose({ openCreate, openEdit, close })

onMounted(() => {
  if (modalEl.value) {
    modalInstance = new Modal(modalEl.value)
    hideListener = () => {
      const active = document.activeElement
      if (active instanceof HTMLElement && modalEl.value?.contains(active)) {
        active.blur()
      }
    }
    modalEl.value.addEventListener('hide.bs.modal', hideListener)
  }
})

onBeforeUnmount(() => {
  if (modalInstance) {
    modalInstance.hide()
    modalInstance = null
  }
  if (modalEl.value && hideListener) {
    modalEl.value.removeEventListener('hide.bs.modal', hideListener)
    hideListener = null
  }
})
</script>

<template>
  <div
    ref="modalEl"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="permissionModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="permissionModalLabel">New Permission</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
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
              <button type="button" class="btn btn-outline-secondary" @click="close">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
