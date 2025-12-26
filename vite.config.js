import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',        // Docker access
        port: 5173,
        strictPort: true,

        // THIS is the key for forum.test
        hmr: {
            host: 'forum.test',
            protocol: 'ws',
            port: 5173,
        },

        // Allow custom domain
        allowedHosts: [
            'forum.test',
        ],
    },
});
