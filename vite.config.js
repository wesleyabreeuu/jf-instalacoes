import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',

        // AdminLTE
        'resources/adminlte/dist/css/adminlte.min.css',
        'resources/adminlte/plugins/jquery/jquery.min.js',
        'resources/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'resources/adminlte/dist/js/adminlte.min.js',
      ],
      refresh: true,
    }),
  ],
});
