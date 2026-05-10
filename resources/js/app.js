import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { useI18n } from './i18n/index.js';
import '../css/app.css';

createInertiaApp({
    title: (title) => title ? `${title} — NEXUS` : 'NEXUS',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        const { t, te, locale } = useI18n();

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia);

        // Make i18n available globally in templates
        app.config.globalProperties.$t = t;
        app.config.globalProperties.$te = te;
        app.config.globalProperties.$locale = locale;

        app.mount(el);
    },
    progress: {
        color: '#6366f1',
        showSpinner: true,
    },
});
