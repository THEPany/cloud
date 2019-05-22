<template>
    <layout title="Crear Factura">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.bills.index', organization.slug)">Facturas</inertia-link>
            <span class="text-green-light font-medium">/</span> Crear
        </h1>
        <div class="bg-white rounded shadow overflow-hidden">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <select-input v-model="form.client_id" :errors="errors.client_id" class="pr-6 pb-8 w-full lg:w-1/5" label="Cliente">
                        <option :value="null">Al contado</option>
                        <option v-for="client in clients" :value="client.id">{{ client.name }} {{ client.last_name }}</option>
                    </select-input>
                    <select-input v-model="form.type_bill" :errors="errors.type_bill" class="pr-6 pb-8 w-full lg:w-1/5" label="Tipo de factura">
                        <option v-for="type in type_bill" :value="type">{{ type }} </option>
                    </select-input>
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
    import SelectInput from '@/Partials/SelectInput'

    export default {
        components: {
            Layout,
            LoadingButton,
            TextInput,
            TextareaInput,
            SelectInput
        },
        props: {
            organization: Object,
            clients: Array,
            type_bill: Array,
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
                    client_id: null,
                    type_bill: null,
                },
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.post(this.route('invoice.bills.store', this.organization.slug), this.form)
                    .then(() => this.sending = false)
            },
        },
    }
</script>