import { defineConfig } from 'vite';

export default defineConfig({
  root: '.',
  publicDir: 'assets/images',
  build: {
    outDir: 'assets/dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        client: 'assets/src/client.ts',
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        assetFileNames: 'css/[name].[ext]',
      },
    },
  },
});
