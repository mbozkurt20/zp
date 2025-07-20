import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { webcrypto } from 'crypto';

// Eğer globalThis.crypto yoksa webcrypto'yu ata
if (!globalThis.crypto) {
    globalThis.crypto = webcrypto;
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
});
