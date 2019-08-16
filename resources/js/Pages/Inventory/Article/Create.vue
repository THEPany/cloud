<template>
    <layout title="Crear Articulo">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-yellow-light hover:text-yellow-dark" :href="route('inventory.articles', organization.slug)">Articulos</inertia-link>
            <span class="text-yellow-light font-medium">/</span> Crear
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input v-model="form.code" :errors="$page.errors.code" class="pr-6 pb-8 w-full lg:w-1/2" label="Código del producto" />
                <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Nombre" />
                <text-input v-model="form.cost" type="number" :errors="$page.errors.cost" class="pr-6 pb-8 w-full lg:w-1/2" label="Costo" />
                <text-input v-model="form.gain" type="number" :errors="$page.errors.gain" class="pr-6 pb-8 w-full lg:w-1/2" label="Ganancia en %" />
                <text-input :value="gain" type="number" readonly class="pr-6 pb-8 w-full lg:w-1/2" label="Ganancia" />
                <text-input :value="price" type="number" readonly class="pr-6 pb-8 w-full lg:w-1/2" label="Precio de venta" />
            </div>
        </div>

        <h2 class="mt-12 font-bold text-2xl">Detalles adicionales</h2>
        <div class="mt-6 bg-white rounded shadow overflow-hidden">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <select-input v-model="form.type" :errors="$page.errors.type" class="pr-6 pb-8 w-full lg:w-1/2" label="Tipo de producto">
                        <option :value="null" />
                        <option value="fisico">Fisico</option>
                        <option value="servicio">Servicio</option>
                    </select-input>
                    <text-input v-model="form.stock" type="number" :errors="$page.errors.stock" class="pr-6 pb-8 w-full lg:w-1/2" label="Cantidad inicial" />
                    <textarea-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Descripcion" />
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                    <loading-button :loading="sending" class="btn-yellow" type="submit">Crear articulo</loading-button>
                </div>
            </form>
        </div>
    </layout>
</template>

<script>
    import Layout from '@/Pages/Inventory/Partials/Layout'
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
            organization: Object
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                form: {
                    code: null,
                    name: null,
                    cost: null,
                    gain: null,
                    description: null,
                    type: null,
                    stock: null,
                    iva: null,
                },
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.post(this.route('inventory.articles.store', this.organization.slug), this.form)
                    .then(() => this.sending = false)
            },
        },
        computed: {
            gain() {
              return this.form.cost * (this.form.gain / 100);
            },
            price() {
              return parseInt(this.form.cost) + this.gain;
            }
        }
    }
</script>
