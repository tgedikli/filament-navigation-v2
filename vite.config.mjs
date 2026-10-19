import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        outDir: 'resources/dist',
        emptyOutDir: true,
        lib: {
            entry: 'resources/js/plugin.js',
            name: 'FilamentNavigation',
            formats: ['iife'],
            fileName: () => 'plugin.js',
            cssFileName: 'plugin',
        },
    },
})
