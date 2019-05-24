<template>
    <layout title="Crear Producto">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.products.index', organization.slug)">Productos</inertia-link>
            <span class="text-green-light font-medium">/</span> Crear
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Nombre" />
                    <text-input v-model="form.cost" type="number" :errors="$page.errors.cost" class="pr-6 pb-8 w-full lg:w-1/2" label="Costo" />
                    <textarea-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Descripcion" />
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                    <loading-button :loading="sending" class="btn-green" type="submit">Crear producto</loading-button>
                </div>
            </form>
        </div>
    </layout>
</template>

<script>
    import Layout from '@/Partials/Invoice/Layout'
    import LoadingButton from '@/Partials/LoadingButton'
    import TextInput from '@/Partials/TextInput'
    import TextareaInput from '@/Partials/TextareaInput'

    export default {
        components: {
            Layout,
            LoadingButton,
            TextInput,
            TextareaInput,
        },
        props: {
            organization: Object
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                form: {
                    name: null,
                    cost: null,
                    description: null,
                },
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.post(this.route('invoice.products.store', this.organization.slug), this.form)
                    .then(() => this.sending = false)
            },
        },
    }
</script>