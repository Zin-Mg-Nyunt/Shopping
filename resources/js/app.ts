import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import type { DefineComponent } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from '@/composables/useAppearance';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { Ziggy } from './ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
        let page = pages[`./pages/${name}.vue`]();
        return page.then((module) => {
            if (module.default.layout === undefined) {
                module.default.layout = DefaultLayout;
            }
            return module;
        });
    },
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(VueApexCharts)
            .mount(el);
    },
});

// This will set light / dark mode on page load...
initializeTheme();
