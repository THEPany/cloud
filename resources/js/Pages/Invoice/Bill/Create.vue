<template>
    <layout title="Crear Factura">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.bills.index', organization.slug)">Facturas</inertia-link>
            <span class="text-green-light font-medium">/</span> Crear
        </h1>
        <div class="bg-white rounded shadow">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <select-search-input v-model="form.client_id" :options="clients" :custom-label="customLabel"
                                     :errors="$page.errors.client_id" class="pr-6 pb-8 w-full lg:w-1/4" label="Cliente" :placeholder="'Cliente al contado'" />
                <select-input v-model="form.bill_type" :errors="$page.errors.bill_type"
                              class="pr-6 pb-8 w-full lg:w-1/4"
                              select-class="vc-shadow vc-appearance-none vc-border vc-rounded vc-w-full vc-py-2 vc-px-3 vc-text-gray-800 vc-bg-white vc-leading-tight focus:vc-outline-none focus:vc-shadow-outline"
                              label="Tipo de factura">
                    <option v-for="type in type_bill" :value="type">{{ type }} </option>
                </select-input>
                <div class="pr-6 pb-8 w-full lg:w-1/4">
                    <label class="form-label">Fecha de expiración:</label>
                    <text-date-picker v-model="date"/>
                    <div v-if="$page.errors.expired_at && $page.errors.expired_at.length" class="form-error">{{ $page.errors.expired_at[0] }}</div>
                </div>
                <text-input v-model="form.discount" :errors="$page.errors.discount"  class="pr-6 pb-8 w-full lg:w-1/4"
                            input-class="vc-shadow vc-appearance-none vc-border vc-rounded vc-w-full vc-py-2 vc-px-3 vc-text-gray-800 vc-bg-white vc-leading-tight focus:vc-outline-none focus:vc-shadow-outline"
                            label="Descuento" type="number" />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">Articulo</th>
                        <th class="px-6 pt-6 pb-4">Descripcion</th>
                        <th class="px-6 pt-6 pb-4">Costo unitario</th>
                        <th class="px-6 pt-6 pb-4">Cantidad</th>
                        <th class="px-6 pt-6 pb-4" colspan="2">Sub total</th>
                    </tr>
                    <tr v-for="(product, index) in form.products" :key="product.id">
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center text-grey-darker">
                                {{ product.name }}
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center text-grey-darker">
                                {{ product.description }}
                            </div>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center text-grey-darker">
                                {{ product.cost }}
                            </div>
                        </td>
                        <td class="border-t">
                            <text-input
                                    v-model="product.quantity"
                                    type="number"
                                    min="1"
                                    max="999999"
                                    class="p-4"
                                    input-class="vc-shadow vc-appearance-none vc-border vc-rounded w-32 vc-py-2 vc-px-3 vc-text-gray-800 vc-bg-white vc-leading-tight focus:vc-outline-none focus:vc-shadow-outline"
                            ></text-input>
                        </td>
                        <td class="border-t">
                            <div class="px-6 py-4 flex items-center text-grey-darker">
                                {{ product.cost * product.quantity }}
                            </div>
                        </td>
                        <td class="border-t w-px">
                            <a @click="removeFields(index)" class="px-4 flex items-center" tabindex="-1">
                                <svg class="block w-6 h-6 fill-grey hover:fill-red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-t px-6 py-4" colspan="2">
                            <multiselect v-model="select" :options="products"
                                         :searchable="true" placeholder="Buscar productos"
                                         :limit="5" :custom-label="productLabel"></multiselect>
                        </td>
                        <td class="border-t px-6 py-4" colspan="2">
                            <text-input class="pr-6" v-model="cantidad"
                                        input-class="vc-shadow vc-appearance-none vc-border vc-rounded vc-w-full vc-py-2 vc-px-3 vc-text-gray-800 vc-bg-white vc-leading-tight focus:vc-outline-none focus:vc-shadow-outline"
                                        type="number"/>
                        </td>
                        <td class="border-t px-6 py-4" colspan="2">
                            <a @click="addFields" class="w-full bg-transparent hover:bg-green text-green hover:text-white border border-green hover:border-transparent font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="block w-6 h-6 fill-current mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M11 9h4v2h-4v4H9v-4H5V9h4V5h2v4zm-1 11a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/></svg>
                                <span class="hidden md:flex">Agregar</span>
                            </a>
                        </td>
                    </tr>
                    <tr v-if="form.products.length > 0">
                        <td class="border-t px-6 py-4" colspan="6">
                            <div class="flex flex-wrap">
                                <div class="w-1/2">
                                    <text-input v-model="form.paid_out" :errors="$page.errors.paid_out" class="pr-6 pb-8 w-full"
                                                input-class="vc-shadow vc-appearance-none vc-border vc-rounded vc-w-full vc-py-2 vc-px-3 vc-text-gray-800 vc-bg-white vc-leading-tight focus:vc-outline-none focus:vc-shadow-outline"
                                                label="Importe" type="number" />
                                </div>
                                <div class="w-1/2 block">
                                    <div class="flex justify-between border-b py-2">
                                        <div>Sub total</div>
                                        <div>{{ subTotal }}</div>
                                    </div>
                                    <div v-if="discount > 0" class="flex justify-between border-b py-2">
                                        <div>Descuento</div>
                                        <div>{{ discount }}</div>
                                    </div>
                                    <div class="flex justify-between border-b py-2">
                                        <div>Pagado hasta la fecha</div>
                                        <div>{{ this.form.paid_out }}</div>
                                    </div>
                                    <div class="flex justify-between border-b py-2 font-bold">
                                        <div> Saldo adeudado</div>
                                        <div>{{ total }}</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="flex justify-center pt-8">
            <form @submit.prevent="submit">
                <loading-button :disabled="form.products.length === 0"
                                :class="{'disabled':form.products.length === 0}"
                                :loading="sending"
                                class="btn-green" type="submit">Crear factura</loading-button>
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
    import SelectSearchInput from '@/Partials/SelectSearchInput'
    import Icon from '@/Partials/Icon'

    export default {
        components: {
            Layout,
            LoadingButton,
            TextInput,
            TextareaInput,
            SelectInput,
            SelectSearchInput,
            Icon
        },
        props: {
            organization: Object,
            clients: Array,
            products: Array,
            type_bill: Array
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                form: {
                    client_id: null,
                    bill_type: null,
                    expired_at: null,
                    discount: null,
                    paid_out: null,
                    products: []
                },
                select: null,
                cantidad: 1,
                date: null
            }
        },
        watch: {
            date(date) {
                if (date) {
                    this.form.expired_at = date.toLocaleDateString().replace(/\//g, '-');
                }else {
                    this.form.expired_at = null
                }
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.post(this.route('invoice.bills.store', this.organization.slug), this.form)
                    .then(() => this.sending = false)
            },
            customLabel ({ name, last_name }) {
                return `${name} ${last_name}`
            },
            productLabel ({ id, name, description}) {
                return `${id}: ${name} - ${description}`
            },
            removeFields(index) {
                this.form.products.splice(index, 1);
            },
            addFields() {
                if (this.select instanceof Object) {
                    let update = this.form.products.find(item => item.id === this.select.id);
                    if (update) {
                        update.quantity = parseInt(update.quantity) + parseInt(this.cantidad);
                    } else {
                        this.form.products.push({
                            'id': this.select.id,
                            'name': this.select.name,
                            'description': this.select.description,
                            'cost': this.select.cost,
                            'quantity': this.cantidad,
                        })
                    }
                }
                this.select = null;
                this.cantidad = 1;
            }
        },
        computed: {
            subTotal() {
                return this.form.products.reduce((subTotal, item) => {
                    return parseInt(subTotal) + (parseInt(item.cost) * parseInt(item.quantity))
                }, 0);
            },
            discount() {
                if (! isNaN(parseInt(this.form.discount))) {
                    return parseInt(this.form.discount);
                }
                return 0;
            },
            total() {
                return this.subTotal - this.discount;
            }
        }
    }
</script>

<style>
    .multiselect__tags {
        width: 100%;
        color: #2d3748;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px 0 rgba(0,0,0,.06);
        padding-left: .75rem;
        padding-right: .75rem;
        line-height: 1.25;
        border-width: 1px;
        border-radius: .25rem;
        background-color: #fff;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
</style>