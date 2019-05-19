<template>
    <div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" href="#')">
                <icon name="home" class="w-4 h-4 mr-2" :class="isUrl($page.organization.slug + '/invoice/home') ? 'fill-white' : 'fill-green-light group-hover:fill-white'" />
                <div :class="isUrl($page.organization.slug + '/invoice/home') ? 'text-white' : 'text-green-lighter group-hover:text-white'">Home</div>
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