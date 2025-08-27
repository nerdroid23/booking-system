import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { run } from 'vite-plugin-run';
import vueDevTools from 'vite-plugin-vue-devtools';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      ssr: 'resources/js/ssr.ts',
      refresh: true,
    }),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    run([
      {
        name: 'typescript-transformer',
        run: ['php', 'artisan', 'typescript:transform', '--format'],
        pattern: ['app/Data/**/*.php'],
      },
    ]),
    vueDevTools({
      appendTo: 'resources/js/app.ts',
    }),
  ],
});
