<template>
    <main>
        <h1>Other {{ $page.organization }}</h1>
        <inertia-link class="hover:text-blue"  :href="route('apps.index', $page.organization)">Other</inertia-link>
    </main>
</template>

<script>
    export default {
        props: {
            organization: String,
        },
    }
</script>