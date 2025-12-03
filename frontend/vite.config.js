import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '@core': fileURLToPath(new URL('./src/core', import.meta.url)),
      '@state': fileURLToPath(new URL('./src/state', import.meta.url)),
      '@pmsg': fileURLToPath(new URL('./src/pmsg', import.meta.url)),
    },
  },
})
