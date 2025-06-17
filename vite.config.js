import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  root: 'assets',
  plugins: [react()],
  build: {
    outDir: '../amd/build',
    emptyOutDir: true,
    strict: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'assets/js/main.jsx'),
      output: {
        entryFileNames: 'main.js',
      },
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'assets/js'),
        },
},
});



  