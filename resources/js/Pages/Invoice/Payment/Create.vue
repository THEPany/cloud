<template>
    <layout title="Crear Pago">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.payments.index', organization.slug)">Pagos</inertia-link>
            <span class="text-green-light font-medium">/</span> Crear
        </h1>
        <div class="bg-white rounded shadow max-w-lg">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <select-search-input class="px-6 py-4 w-full"
                                     v-model="client"
                                     :options="clients"
                                     :returnObject="true"
                                     label="Buscar cliente">
                    <template slot="singleLabel" slot-scope="props">
                        <span class="option__title">{{ props.slotScope.name + ' ' + props.slotScope.last_name }}</span>
                    </template>
                    <template slot="multipleLabel" slot-scope="props">
                        <span class="option__title font-bold">{{ props.slotScope.name + ' ' + props.slotScope.last_name }} | {{ props.slotScope.id_card }}</span>
                        <br>
                        <span class="option__small text-xs">Monto Pendiente {{ props.slotScope.pending }}</span>
                    </template>
                </select-search-input>
            </div>
            <div v-if="client" class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">Cliente</label>
                    <p>{{ this.client.name + ' ' + this.client.last_name }}</p>
                </div>
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">Cedula</label>
                    <p>{{ this.client.id_card }}</p>
                </div>
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">Pediente</label>
                    <p>{{ this.client.pending | currency }}</p>
                </div>
            </div>
        </div>

        <div v-if="client" class="bg-white rounded shadow overflow-x-auto mt-24">
            <table class="w-full whitespace-no-wrap">
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">Factura</th>
                    <th class="px-6 pt-6 pb-4">Total</th>
                    <th class="px-6 pt-6 pb-4">Pendiente</th>
                    <th class="px-6 pt-6 pb-4">Monto Abonado</th>
                </tr>
                <tr v-for="bill in form.bills" :key="bill.id">
                    <td class="border-t">
                        <inertia-link :href="route('invoice.bills.show', {'slug':organization.slug, 'bill':bill.id})" class="px-6 py-4 flex items-center text-blue hover:text-green">
                            No. Factura {{ bill.id }}
                        </inertia-link>
                    </td>
                    <td class="border-t">
                        <div class="px-6 py-4 flex items-center text-grey-darker">
                            {{ bill.total | currency }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="px-6 py-4 flex items-center text-grey-darker">
                            {{ bill.pending | currency }}
                        </div>
                    </td>
                    <td class="border-t">
                        <text-input v-model="bill.paid_out"
                                    type="number"
                                    min="1"
                                    :max="bill.pending"
                                    oninput="validity.valid || (value='');"
                                    input-class="vc-shadow vc-appearance-none vc-border vc-rounded w-full vc-py-2 vc-px-3 vc-text-gray-800 vc-bg-white vc-leading-tight focus:vc-outline-none focus:vc-shadow-outline"
                                    class="px-6 py-4 w-32"/>
                    </td>
                </tr>
            </table>
            <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                <form @submit.prevent="submit">
                    <loading-button :loading="sending" class="btn-green" type="submit">Crear Pago</loading-button>
                </form>
            </div>
        </div>
    </layout>
</template>

<script>
  import Layout from '@/Pages/Invoice/Partials/Layout'
  import LoadingButton from '@/Partials/LoadingButton'
  import TextInput from '@/Partials/TextInput'
  import TextareaInput from '@/Partials/TextareaInput'
  import SelectSearchInput from '@/Partials/SelectSearchInput'

  export default {
    components: {
      Layout,
      LoadingButton,
      TextInput,
      TextareaInput,
      SelectSearchInput
    },
    props: {
      organization: Object,
      clients: Array
    },
    remember: 'form',
    data() {
      return {
        sending: false,
        client: null,
        form: {
          bills: null,
        },
      }
    },
    watch: {
      client(value) {
        if (value) this.form.bills = value.bills
      }
    },
    methods: {
      submit() {
        this.sending = true
        this.$inertia.post(this.route('invoice.payments.store', this.organization.slug), this.form)
          .then(() => this.sending = false)
      },
    },
  }
</script>
