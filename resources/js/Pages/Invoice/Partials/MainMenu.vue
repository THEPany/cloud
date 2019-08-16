<template>
    <div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('invoice.home.index', $page.organization.slug)">
                <icon name="home" class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/invoice/home') ? 'fill-white' : 'fill-green-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/invoice/home') ? 'text-white' : 'text-green-lighter group-hover:text-white'">Home</div>
            </inertia-link>
        </div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('invoice.bills.index', $page.organization.slug)">
                <icon name="invoice"  class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/invoice/bills') ? 'fill-white' : 'fill-green-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/invoice/bills') ? 'text-white' : 'text-green-lighter group-hover:text-white'">Facturas</div>
            </inertia-link>
        </div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('invoice.payments.index', $page.organization.slug)">
                <icon name="payment"  class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/invoice/payments') ? 'fill-white' : 'fill-green-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/invoice/payments') ? 'text-white' : 'text-green-lighter group-hover:text-white'">Pagos</div>
            </inertia-link>
        </div>
    </div>
</template>

<script>
    import Icon from '@/Partials/Icon'
    export default {
        components: {
            Icon,
        },
        methods: {
            isUrl(...urls) {
                if (urls[0] === '') {
                    return location.pathname.substr(1) === ''
                }
                return urls.filter(url => location.pathname.substr(1).startsWith(url)).length
            },
        },
    }
</script>
