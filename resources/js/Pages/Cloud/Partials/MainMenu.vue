<template>
    <div>

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
