<template>
    <div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('person.home', $page.organization.slug)">
                <icon name="home" class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/person/home') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/person/home') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Home</div>
            </inertia-link>
        </div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('person.clients', $page.organization.slug)">
                <icon name="client" class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/person/clients') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/person/clients') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Clientes</div>
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
