import "./bootstrap";

import { createApp, h } from "vue";
import { createInertiaApp, Link, Head } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import AppLayout from "@/layouts/AppLayout.vue";

createInertiaApp({
    title: (title) => `CTC DTR  ${title}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        page.default.layout = page.default.layout || AppLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
});
