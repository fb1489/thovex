import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  root: path.resolve(__dirname),
  base: '/dist/',
  build: {
    outDir: path.resolve(__dirname, '../../webroot/dist'),
    emptyOutDir: true,
    rollupOptions: {
      // 👇 Explicitly define your entry point
      input: path.resolve(__dirname, 'main.js'),
      output: {
        entryFileNames: 'main.js',
        assetFileNames: '[name].[ext]',
      },
    },
  },
  server: {
    port: 5173,
    strictPort: true,
    cors: true,
  },
  define: {
    __VUE_PROD_DEVTOOLS__: true,
  },
  resolve: {
    alias: {
      '@': '../../webroot/js'
    }
  }
})
