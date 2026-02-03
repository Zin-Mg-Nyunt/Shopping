import { createInertiaApp, router } from '@inertiajs/vue3';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';
import DefaultLayout from './layouts/DefaultLayout.vue';
import { Ziggy } from './ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
        let page = pages[`./pages/${name}.vue`]();
        return page.then((module) => {
            // Persistent Layout သတ်မှတ်ခြင်း
            if (module.default.layout === undefined) {
                module.default.layout = DefaultLayout;
            }
            return module;
        });
    },
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#f97316',
    },
});

router.on('success', (event) => {
    const cartCount = event.detail.page.props.cart_count;
    if (cartCount != undefined) {
        localStorage.setItem('cart_count', String(cartCount));
        window.dispatchEvent(new Event('cart-count-updated'));
    }
});

// This will set light / dark mode on page load...
initializeTheme();
