<template>
    <layout :title="'Factura ' + bill.id">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.bills.index', organization.slug)">Facturas</inertia-link>
            <span class="text-green-light font-medium">/</span>
            Factura {{ bill.id }}
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-full">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Factura</label>
                    <p>{{ this.bill.id }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Cliente</label>
                    <p>{{ this.bill.client }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Estado</label>
                    <p>{{ this.bill.status }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Sub Total</label>
                    <p>{{ this.bill.sub_total | currency }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Total</label>
                    <p>{{ this.bill.total | currency }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Pendiente</label>
                    <p>{{ this.bill.due_amount | currency }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Fecha de creacion</label>
                    <p>{{ this.bill.created_at }}</p>
                </div>
                <div class="pr-6 pb-8 w-full md:w-1/3">
                    <label class="mb-2 block text-grey-dark font-bold">Fecha de vencimiento</label>
                    <p>{{ this.bill.expired_at }}</p>
                </div>
            </div>
        </div>


        <div class="bg-white rounded shadow overflow-hidden max-w-full mt-12">
            <div class="flex justify-between items-center border-b">
                <div class="text-center px-4 py-2 m-2">Preview</div>
                <div class="text-center px-4 py-2 m-2">
                    <a target="_blank" :href="route('invoice.bills.preview', {'slug':organization.slug,'bill':bill})" class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                    </a>
                </div>
            </div>
            <div class="p-8 flex flex-wrap">
                <iframe class="w-full h-screen" :src="route('invoice.bills.preview', {'slug':organization.slug,'bill':bill})"></iframe>
            </div>

        </div>
    </layout>
</template>

<script>
  import Icon from '@/Partials/Icon'
  import Layout from '@/Pages/Invoice/Partials/Layout'
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
      bill: Object
    },
  }
</script>
