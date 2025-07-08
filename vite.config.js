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
  server: {
    port: 5173, // o el puerto que prefieras
    strictPort: true,
    open: false,
    // Proxy para que las peticiones AJAX sigan funcionando en Moodle
    proxy: {
      // Cambia esto según tu URL de Moodle
      '/lib': 'http://localhost:8080',
      '/local': 'http://localhost:8080',
      '/theme': 'http://localhost:8080',
      '/pluginfile.php': 'http://localhost:8080',
    }
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
