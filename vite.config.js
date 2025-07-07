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

    lib: {
      entry: path.resolve(__dirname, 'assets/js/main.jsx'),
      name:  'aistrix',
      formats: ['amd'],
      fileName: () => 'main'
    },

    rollupOptions: {
      output: {
        amd: { id: 'local_aistrix/main' },
        entryFileNames: '[name].js',
        exports: 'named',
        assetFileNames: '[name].[ext]'
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'assets'),
        },
  },
  define: {
    'process.env.NODE_ENV': '"production"', 
    'process.env': '{}',                  
  },
});
