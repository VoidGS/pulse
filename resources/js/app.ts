import './bootstrap';
import '../css/app.css';

import { createApp, type DefineComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { defineRoutes } from "momentum-trail";
import routes from "../scripts/routes/routes.json";
import { createNotivue } from "notivue";

import 'notivue/notifications.css';
import 'notivue/animations.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const notivue = createNotivue({ position: "top-right" })

defineRoutes(routes);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(notivue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
