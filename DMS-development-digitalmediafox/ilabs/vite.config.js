import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/css/theme.css',
                'resources/assets/js/theme.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/assets'),
        },
    },
    build: {
        sourcemap: false,  // Disable source maps in production
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'resources/assets/js/theme.js'),
                styles: path.resolve(__dirname, 'resources/assets/css/theme.css'),
            },
        },
    },
    assetsInclude: ['**/*.eot', '**/*.woff', '**/*.ttf', '**/*.svg', '**/*.png', '**/*.jpg', '**/*.jpeg'],
});
