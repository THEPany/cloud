<template>
    <layout :title="'No. Pago ' + payment.id">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.payments.index', organization.slug)">Pagos</inertia-link>
            <span class="text-green-light font-medium">/</span> No. Pago {{ payment.id }}
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">No. Pago</label>
                    <p>{{ payment.id }}</p>
                </div>
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">Factura a la que pertenece</label>
                    <p>{{ payment.id }}</p>
                </div>
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">Fecha Pago</label>
                    <p>{{ payment.created_at }}</p>
                </div>
                <div class="px-6 pb-4 w-full md:w-1/2">
                    <label class="mb-2 block text-grey-dark font-bold">Cantidad Recivida</label>
                    <p>{{ payment.paid_out | currency }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto mt-12">
            <table class="w-full whitespace-no-wrap">
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">Factura</th>
                    <th class="px-6 pt-6 pb-4">Fecha de creacion</th>
                    <th class="px-6 pt-6 pb-4">Fecha de expiración</th>
                </tr>
                <tr class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
                    <td class="border-t">
                        <inertia-link class="px-6 py-4 flex items-center focus:text-green" :href="route('invoice.bills.show', {'slug':organization.slug, 'bill':bill})">
                            {{ bill.id }}
                        </inertia-link>
                    </td>
                    <td class="border-t">
                        <inertia-link class="px-6 py-4 flex items-center focus:text-green" :href="route('invoice.bills.show', {'slug':organization.slug, 'bill':bill})">
                            {{ bill.created_at }}
                        </inertia-link>
                    </td>
                    <td class="border-t">
                        <inertia-link class="px-6 py-4 flex items-center focus:text-green" :href="route('invoice.bills.show', {'slug':organization.slug, 'bill':bill})">
                            {{ bill.expired_at }}
                        </inertia-link>
                    </td>
                </tr>
            </table>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto mt-12">
            <div class="flex justify-between items-center border-b">
                <div class="text-center px-4 py-2 m-2">Preview</div>
                <div class="text-center px-4 py-2 m-2">
                    <a target="_blank" :href="route('invoice.payments.preview', {'slug':organization.slug,'payment':payment})" class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                    </a>
                </div>
            </div>
            <div class="p-8 flex flex-wrap">
                <iframe class="w-full h-screen" :src="route('invoice.payments.preview', {'slug':organization.slug,'payment':payment})"></iframe>
            </div>
        </div>
    </layout>
</template>

<script>
  import Layout from '@/Partials/Invoice/Layout'

  export default {
    components: {
      Layout
    },
    props: {
      organization: Object,
      payment: Object,
      bill: Object
    }
  }
</script>