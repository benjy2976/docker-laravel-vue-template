<script setup>
import { computed, ref, onMounted, onBeforeUnmount, defineExpose } from 'vue'
import { Modal } from 'bootstrap'
import { Role } from '@core/admin/role'
import { useRoleStore } from '@stores/admin/roles'
import { usePermissionStore } from '@stores/admin/permissions'
import { useToast } from '@/shared/toast'

const modalEl = ref(null)
let modalInstance = null
let hideListener = null
const roleStore = useRoleStore()
const permissionStore = usePermissionStore()
const toast = useToast()

const defaultRole = () => Role.getDefault()
const form = ref({ ...defaultRole(), permissions: [] })
const mode = ref('create') // 'create' | 'edit'
const selectedAvailable = ref([])
const selectedAssigned = ref([])
const errors = ref({})

const submit = async () => {
  errors.value = {}
  try {
    if (mode.value === 'edit') {
      await roleStore.update(form.value)
      toast.success('Rol actualizado')
    } else {
      await roleStore.create(form.value)
      toast.success('Rol creado')
    }
    close()
  } catch (err) {
    const data = err?.response?.data
    if (err?.response?.status === 422 && data?.errors) {
      errors.value = data.errors
    } else {
      toast.danger('No se pudo guardar el rol')
    }
  }
}

const openCreate = () => {
  mode.value = 'create'
  form.value = { ...defaultRole(), permissions: [] }
  errors.value = {}
  selectedAvailable.value = []
  selectedAssigned.value = []
  modalInstance?.show()
}

const openEdit = (role) => {
  mode.value = 'edit'
  const permIds = (role.permissions || []).map(p => p.id)
  form.value = { ...defaultRole(), ...role, permissions: permIds }
  errors.value = {}
  selectedAvailable.value = []
  selectedAssigned.value = []
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
  if (!permissionStore.list.length) {
    permissionStore.get()
  }
})

const availablePermissions = computed(() => {
  const selected = form.value.permissions || []
  return permissionStore.list
    .filter(p => !selected.includes(p.id))
    .slice()
    .sort((a, b) => a.name.localeCompare(b.name))
})

const assignedPermissions = computed(() => {
  const selected = form.value.permissions || []
  return permissionStore.list
    .filter(p => selected.includes(p.id))
    .slice()
    .sort((a, b) => a.name.localeCompare(b.name))
})

const assignSelected = () => {
  const merged = new Set([...(form.value.permissions || []), ...selectedAvailable.value])
  form.value.permissions = Array.from(merged)
  selectedAvailable.value = []
}

const removeSelected = () => {
  form.value.permissions = (form.value.permissions || []).filter(id => !selectedAssigned.value.includes(id))
  selectedAssigned.value = []
}

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
    aria-labelledby="roleModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="roleModalLabel">
            {{ mode === 'edit' ? 'Editar rol' : 'Nuevo rol' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="card card-body" @submit.prevent="submit">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="roleName">Nombre</label>
                <input
                  id="roleName"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="admin"
                />
                <div v-if="errors.name" class="text-danger small mt-1">
                  {{ errors.name[0] }}
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Permisos</label>
                <div class="row g-2">
                  <div class="col-md-5">
                    <div class="form-text mb-1">Disponibles</div>
                    <select
                      v-model="selectedAvailable"
                      class="form-select"
                      multiple
                      size="8"
                    >
                      <option
                        v-for="perm in availablePermissions"
                        :key="perm.id"
                        :value="perm.id"
                      >
                        {{ perm.name }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-2 d-flex flex-column justify-content-center align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary w-100" @click="assignSelected">
                      &gt;
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100" @click="removeSelected">
                      &lt;
                    </button>
                  </div>
                  <div class="col-md-5">
                    <div class="form-text mb-1">Asignados</div>
                    <select
                      v-model="selectedAssigned"
                      class="form-select"
                      multiple
                      size="8"
                    >
                      <option
                        v-for="perm in assignedPermissions"
                        :key="perm.id"
                        :value="perm.id"
                      >
                        {{ perm.name }}
                      </option>
                    </select>
                    <div v-if="errors.permissions" class="text-danger small mt-1">
                      {{ errors.permissions[0] }}
                    </div>
                  </div>
                </div>
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
