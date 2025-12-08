<script setup>
import { ref, onMounted, onBeforeUnmount, defineExpose } from 'vue'
import { Modal } from 'bootstrap'
import { Permission } from '@core/admin/permission'
import { usePermissionStore } from '@stores/admin/permissions'
import { useToast } from '@/shared/toast'

const modalEl = ref(null)
let modalInstance = null
const permissionStore = usePermissionStore()
let hideListener = null
const toast = useToast()

const defaultPermission = () => Permission.getDefault()
const form = ref(defaultPermission())
const mode = ref('create') // 'create' | 'edit'
const errors = ref({})

const submit = async () => {
  errors.value = {}
  try {
    if (mode.value === 'edit') {
      await permissionStore.update(form.value)
      toast.success('Permiso actualizado')
    } else {
      await permissionStore.create(form.value)
      toast.success('Permiso creado')
    }
    close()
  } catch (err) {
    const data = err?.response?.data
    if (err?.response?.status === 422 && data?.errors) {
      errors.value = data.errors
    } else {
      toast.danger('No se pudo guardar el permiso')
    }
  }
}

const openCreate = () => {
  mode.value = 'create'
  form.value = defaultPermission()
  errors.value = {}
  modalInstance?.show()
}

const openEdit = (permission) => {
  mode.value = 'edit'
  form.value = { ...defaultPermission(), ...permission }
  errors.value = {}
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
          <h5 class="modal-title" id="permissionModalLabel">
            {{ mode === 'edit' ? 'Editar permiso' : 'Nuevo permiso' }}
          </h5>
          <h5 class="modal-title" id="permissionModalLabel">Permiso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="card card-body" @submit.prevent="submit">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="permissionName">Nombre</label>
                <input
                  id="permissionName"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="permissions.view"
                />
                <div v-if="errors.name" class="text-danger small mt-1">{{ errors.name[0] }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="permissionLabel">Etiqueta</label>
                <input
                  id="permissionLabel"
                  v-model="form.menu_label"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.menu_label }"
                  placeholder="Permisos"
                />
                <div v-if="errors.menu_label" class="text-danger small mt-1">{{ errors.menu_label[0] }}</div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" for="permissionDescription">Descripción</label>
                <textarea
                  id="permissionDescription"
                  v-model="form.description"
                  rows="2"
                  class="form-control"
                  :class="{ 'is-invalid': errors.description }"
                  placeholder="Descripción del permiso"
                ></textarea>
                <div v-if="errors.description" class="text-danger small mt-1">{{ errors.description[0] }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="permissionPath">Ruta</label>
                <input
                  id="permissionPath"
                  v-model="form.menu_path"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.menu_path }"
                  placeholder="/permissions"
                />
                <div v-if="errors.menu_path" class="text-danger small mt-1">{{ errors.menu_path[0] }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="permissionIcon">Icono</label>
                <input
                  id="permissionIcon"
                  v-model="form.icon"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.icon }"
                  placeholder="bi-shield-lock"
                />
                <div v-if="errors.icon" class="text-danger small mt-1">{{ errors.icon[0] }}</div>
              </div>
            </div>
            <div class="mt-3 d-flex gap-2">
              <button type="submit" class="btn btn-primary">Guardar</button>
              <button type="button" class="btn btn-outline-secondary" @click="close">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
