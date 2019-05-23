import Inertia from 'inertia-vue'
import PortalVue from 'portal-vue'
import Vue from 'vue'
import Multiselect from 'vue-multiselect'
import VCalendar from 'v-calendar'

Vue.config.productionTip = false
Vue.mixin({ methods: { route: (...args) => window.route(...args).url() } })
Vue.use(Inertia)
Vue.use(PortalVue)
Vue.component('multiselect', Multiselect)
Vue.use(VCalendar, {
    componentPrefix: 'text',
})



let app = document.getElementById('app')

new Vue({
    render: h => h(Inertia, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(app)