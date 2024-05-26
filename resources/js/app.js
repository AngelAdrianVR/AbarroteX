import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import VueApexCharts from "vue3-apexcharts";
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import locale from 'element-plus/dist/locale/es.mjs';

// iniciar IndexedDB
import { initializeProducts } from './dbService.js';
initializeProducts(); //abre conexion a indexedDB y revisa si hay mas productos en server que en local. De ser asi, actualiza la BDD local

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueApexCharts)
            .use(ZiggyVue)
            .use(ElementPlus, {locale})
            .mount(el);
    },
    progress: {
        color: '#F68C0F',
    },
});
