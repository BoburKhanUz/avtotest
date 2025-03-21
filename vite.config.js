import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            output: {
                // Assetlarni relative path bilan saqlash
                assetFileNames: 'assets/[name][extname]',
                chunkFileNames: 'assets/[name].js',
                entryFileNames: 'assets/[name].js',
            },
        },
    },
    // Ngrok uchun base URL sozlash
    base: '', // Standard holda bo'sh qoldiriladi, lekin global URL uchun dinamik sozlash mumkin
    server: {
        host: '0.0.0.0', // Hamma interfeyslar uchun ishlaydi
        port: 8000,
        hmr: {
            host: 'localhost', // HMR uchun lokal host
        },
    },
});