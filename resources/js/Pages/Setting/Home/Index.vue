<template>
    <layout title="Home">
        <h1 class="mb-8 font-bold text-3xl">Home</h1>
    </layout>
</template>

<script>
  import Layout from '@/Pages/Setting/Partials/Layout'

  export default {
    components: {
      Layout
    },
    props: {
      organization: Object,
    }
  }
</script>
