<template>
    <layout title="Invoice Home">
        <h1 class="mb-8 font-bold text-3xl">Home</h1>
    </layout>
</template>

<script>
    import Layout from '@/Partials/Invoice/Layout'

    export default {
        components: {
            Layout
        },
        props: {
            organization: Object,
        }
    }
</script>