<script setup>
import Navbar from './Navbar.vue'
import Content from './Content.vue'
import ThemeToggle from '@/layout/default/ThemeToggle.vue'

const props = defineProps({
  userName    : { type: String, default: 'Usuario' },
  userEmail   : { type: String, default: '' },
  currentPath : { type: String, default: '/' },
  theme       : { type: String, default: 'auto' },
})

const emit = defineEmits(['settings', 'logout', 'change-theme'])
</script>

<template>
  <div>
    <Navbar @settings="emit('settings')" @logout="emit('logout')" />
    <div class="container-fluid">
      <div class="row">
        <Content>
          <slot ></slot>
        </Content>
      </div>
    </div>
  </div>

  <ThemeToggle :theme="props.theme" @change-theme="emit('change-theme', $event)" />
</template>
