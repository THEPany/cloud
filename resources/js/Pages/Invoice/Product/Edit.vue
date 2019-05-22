<template>
    <layout :title="form.name">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.products.index', organization.slug)">Productos</inertia-link>
            <span class="text-green-light font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="product.deleted_at" class="mb-6" @restore="restore">
            Este producto ha sido eliminado.
        </trashed-message>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.name" :errors="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Nombre"/>
                    <text-input v-model="form.cost" :errors="errors.cost" class="pr-6 pb-8 w-full lg:w-1/2" label="Costo"/>
                    <textarea-input v-model="form.description" :errors="errors.description" class="pr-6 pb-8 w-full" label="Descripcion" />
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <button v-if="!product.deleted_at" class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">Eliminar producto</button>
                    <loading-button :loading="sending" class="btn-green ml-auto" type="submit">Actualizar producto</loading-button>
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
            product: Object,
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
                    name: this.product.name,
                    cost: this.product.cost,
                    description: this.product.description,
                },
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.put(this.route('invoice.products.update', {'slug':this.organization.slug,'product':this.product.id}), this.form)
                    .then(() => this.sending = false)
            },
            destroy() {
                if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                    this.$inertia.delete(this.route('invoice.products.update', {'slug':this.organization.slug,'product':this.product.id}))
                }
            },
            restore() {
                if (confirm('¿Estás seguro de que quieres restaurar este producto?')) {
                    this.$inertia.put(this.route('invoice.products.restore', {'slug':this.organization.slug,'product':this.product.id}))
                }
            }
        },
    }
</script>