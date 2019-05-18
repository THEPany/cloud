<template>
    <div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('home.index')">
                <icon name="home" class="w-4 h-4 mr-2" :class="isUrl('home') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
                <div :class="isUrl('home') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Home</div>
            </inertia-link>
        </div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('organizations.index')">
                <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('organizations') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
                <div :class="isUrl('organizations') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Organizations</div>
            </inertia-link>
        </div>
        <div class="mb-4">
            <inertia-link class="flex items-center group py-3" :href="route('subscriptions.index')">
                <icon name="wallet" class="w-4 h-4 mr-2" :class="isUrl('subscriptions') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
                <div :class="isUrl('subscriptions') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Subscriptions</div>
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