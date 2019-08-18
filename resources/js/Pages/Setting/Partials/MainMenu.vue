<template>
    <div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('setting.home', $page.organization.slug)">
                <icon name="home" class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/setting/home') ? 'fill-white' : 'fill-grey-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/setting/home') ? 'text-white' : 'text-grey-lighter group-hover:text-white'">Home</div>
            </inertia-link>
        </div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('setting.permissions', $page.organization.slug)">
                <icon name="client" class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/setting/permissions') ? 'fill-white' : 'fill-grey-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/setting/permissions') ? 'text-white' : 'text-grey-lighter group-hover:text-white'">Permisos</div>
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
