import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: true,                // listen on all addresses (0.0.0.0)
        port: 5173,
        strictPort: false,
        cors: true,                // enable Access-Control-Allow-Origin: *
        hmr: {
            host: '192.168.1.25',    // IP of your dev machine as seen by browser
            protocol: 'ws',
        },
    },
});
