import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import DefaultLayout from '@/layouts/DefaultLayout.vue';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy';
import { createApp, DefineComponent, h } from 'vue';

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
            .mount(el);
    },
});

// This will set light / dark mode on page load...
initializeTheme();
