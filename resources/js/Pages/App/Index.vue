<template>
    <main>
        <a :href="route('home.index')">Home</a>
        <br>
        <h1>{{ $page.organization }}</h1>
        <inertia-link class="hover:text-blue" :href="route('apps.other', $page.organization)">Other</inertia-link>
    </main>
</template>

<script>
    export default {
        props: {
            organization: String,
        },
    }
</script>