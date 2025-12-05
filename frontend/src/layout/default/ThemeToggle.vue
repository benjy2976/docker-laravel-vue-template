<script setup>
const props = defineProps({
  theme : { type: String, default: 'auto' },
})

const emit = defineEmits(['change-theme'])
</script>

<template>
  <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
    <button
      class="btn btn-primary rounded-pill shadow dropdown-toggle"
      type="button"
      data-bs-toggle="dropdown"
      aria-expanded="false"
    >
      <span v-if="props.theme === 'light'">☀️</span>
      <span v-else-if="props.theme === 'dark'">🌙</span>
      <span v-else>⚙️</span>
    </button>
    <ul class="dropdown-menu">
      <li>
        <button class="dropdown-item" :class="{ active: props.theme === 'light' }" @click="emit('change-theme', 'light')">
          Light
        </button>
      </li>
      <li>
        <button class="dropdown-item" :class="{ active: props.theme === 'dark' }" @click="emit('change-theme', 'dark')">
          Dark
        </button>
      </li>
      <li>
        <button class="dropdown-item" :class="{ active: props.theme === 'auto' }" @click="emit('change-theme', 'auto')">
          Auto
        </button>
      </li>
    </ul>
  </div>
</template>
