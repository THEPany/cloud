<template>
    <layout title="Crear Producto">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.clients.index', organization.slug)">Clientes</inertia-link>
            <span class="text-green-light font-medium">/</span> Crear
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.name" :errors="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Nombre" />
                    <text-input v-model="form.last_name" :errors="errors.last_name" class="pr-6 pb-8 w-full lg:w-1/2" label="Apellido" />
                    <text-input v-model="form.id_card" :errors="errors.id_card" class="pr-6 pb-8 w-full lg:w-1/2" label="Cedula" />
                    <text-input type="email" v-model="form.email" :errors="errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Correo electrónico" />
                    <text-input v-model="form.phone" :errors="errors.phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Telefono" />
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                    <loading-button :loading="sending" class="btn-green" type="submit">Crear cliente</loading-button>
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
            organization: Object,
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                form: {
                    name: null,
                    last_name: null,
                    id_card: null,
                    email: null,
                    phone: null,
                },
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.post(this.route('invoice.clients.store', this.organization.slug), this.form)
                    .then(() => this.sending = false)
            },
        },
    }
</script>