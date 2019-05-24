<template>
    <layout :title="form.name">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.clients.index', organization.slug)">Clientes</inertia-link>
            <span class="text-green-light font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="client.deleted_at" class="mb-6" @restore="restore">
            Este cliente ha sido eliminado.
        </trashed-message>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Nombre" />
                    <text-input v-model="form.last_name" :errors="$page.errors.last_name" class="pr-6 pb-8 w-full lg:w-1/2" label="Apellido" />
                    <text-input v-model="form.id_card" :errors="$page.errors.id_card" class="pr-6 pb-8 w-full lg:w-1/2" label="Cedula" />
                    <text-input type="email" v-model="form.email" :errors="$page.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Correo electrónico" />
                    <text-input v-model="form.phone" :errors="$page.errors.phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Telefono" />
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <button v-if="!client.deleted_at" class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">Eliminar cliente</button>
                    <loading-button :loading="sending" class="btn-green ml-auto" type="submit">Actualizar cliente</loading-button>
                </div>
            </form>
        </div>
    </layout>
</template>

<script>
    import Icon from '@/Partials/Icon'
    import Layout from '@/Partials/Invoice/Layout'
    import LoadingButton from '@/Partials/LoadingButton'
    import SelectInput from '@/Partials/SelectInput'
    import TextInput from '@/Partials/TextInput'
    import TrashedMessage from '@/Partials/TrashedMessage'
    import TextareaInput from '@/Partials/TextareaInput';

    export default {
        components: {
            TextareaInput,
            Icon,
            Layout,
            LoadingButton,
            SelectInput,
            TextInput,
            TrashedMessage
        },
        props: {
            organization: Object,
            client: Object
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                form: {
                    name: this.client.name,
                    last_name: this.client.last_name,
                    id_card: this.client.id_card,
                    email: this.client.email,
                    phone: this.phone,
                },
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.put(this.route('invoice.clients.update', {'slug':this.organization.slug,'client':this.client.id}), this.form)
                    .then(() => this.sending = false)
            },
            destroy() {
                if (confirm('¿Estás seguro de que quieres eliminar este cliente?')) {
                    this.$inertia.delete(this.route('invoice.clients.update', {'slug':this.organization.slug,'client':this.client.id}))
                }
            },
            restore() {
                if (confirm('¿Estás seguro de que quieres restaurar este cliente?')) {
                    this.$inertia.put(this.route('invoice.clients.restore', {'slug':this.organization.slug,'client':this.client.id}))
                }
            }
        },
    }
</script>