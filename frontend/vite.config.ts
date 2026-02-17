import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [vue(), tailwindcss()],
  base: '/',
  build: {
    outDir: '../dist',
    emptyOutDir: true,
  },
  server: {
    allowedHosts: true,
    port: 5173,
    proxy: {
      '/api.php': 'http://localhost:8080',
      '/api': 'http://localhost:8080',
    },
  },
})
