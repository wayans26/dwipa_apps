import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/Master/index.js',
                'resources/js/Master/index_login.js',
            ],
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
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        target: 'esnext',
        sourcemap: false,
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    // if (id.includes('node_modules')) {
                    //     return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    // };
                    // if (
                    //     id.includes('resurces/js/Page/welcome.vue') ||
                    //     id.includes('resurces/js/Page/asset_register.vue') ||
                    //     id.includes('resurces/js/Page/asset_register_add.vue') ||
                    //     id.includes('resurces/js/Page/asset_register_edit.vue')
                    // ) {
                    //     return 'asset_register';
                    // }
                    vue: ["vue", "vue-router"],
                    sweetalert: ["sweetalert2"],
                    apexcharts: ["apexcharts", "vue3-apexcharts"],
                    vendor: ['js-file-download', 'vue3-carousel', '@chenfengyuan/vue-barcode', 'lodash.debounce', 'yup', 'vee-validate']
                },
            },
        },
    },
    esbuild: {
        drop: ["console", "debugger"], // optional, kecilkan bundle
    },
});
