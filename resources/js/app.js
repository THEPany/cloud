import { InertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import Vue from 'vue'
import Multiselect from 'vue-multiselect'
import VCalendar from 'v-calendar'
import Vue2Filters from 'vue2-filters';

Vue.config.productionTip = false
Vue.mixin({ methods: { route: window.route } })
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.component('multiselect', Multiselect)
Vue.use(VCalendar, {
    componentPrefix: 'text',
})
Vue.use(Vue2Filters)

let app = document.getElementById('app')

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)
