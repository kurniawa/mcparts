import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // server: {
    //     host: 'mcparts-sas-v2.test',
    //     port: 5173,
    //     origin: 'http://mcparts-sas-v2.test:5173', // <- penting
    //     hmr: {
    //         host: 'mcparts-sas-v2.test',
    //         protocol: 'http',
    //         port: 5173,
    //     },
    // },
});
