<script setup>
import { ref } from 'vue'

const emit = defineEmits(['search'])
const termino = ref('')

const enviar = () => emit('search', termino.value.trim())
const limpiar = () => {
  termino.value = ''
  emit('search', '')
}
</script>

<template>
  <form class="mb-0" @submit.prevent="enviar">
    <div class="d-flex justify-content-end">
      <div class="search-box">
        <div class="input-group">
          <input
            id="permissionSearch"
            v-model="termino"
            type="text"
            class="form-control"
            placeholder="Buscar permisos"
            @keyup.enter.prevent="enviar"
          />
          <button
            v-if="termino"
            class="btn btn-outline-secondary"
            type="button"
            aria-label="Limpiar"
            @click="limpiar"
          >
            <i class="bi bi-x-lg"></i>
          </button>
          <button class="btn btn-primary" type="submit" aria-label="Buscar">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>
  </form>
</template>
