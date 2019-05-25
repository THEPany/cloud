<template>
    <layout :title="'Factura ' + bill.id">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.bills.index', organization.slug)">Facturas</inertia-link>
            <span class="text-green-light font-medium">/</span>
            Factura {{ bill.id }}
        </h1>
        <div v-if="bill.status === 'PAGADA'" class="mb-4 p-4 bg-blue-light rounded border border-blue-dark flex items-center justify-between">
            <div class="flex items-center">
                <icon name="exclamation" class="flex-no-shrink w-4 h-4 fill-blue-darker mr-2" />
                <div class="text-blue-darker">
                    Esta factura ya sido pagada.
                </div>
            </div>
        </div>
        <a class="mb-4 bg-white hover:bg-grey-light font-bold py-3 px-4 rounded inline-flex items-center" target="_blank" :href="route('invoice.bills.show', {'slug':organization.slug,'bill':bill})">
            <icon name="invoice"  class="fill-current w-4 h-4 mr-2"  />
            <span>Imprimir</span>
        </a>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.client" class="pr-6 pb-8 w-full lg:w-1/2" input-class="form-input bg-grey-lighter" label="Cliente" disabled/>
                    <text-input v-model="form.id" class="pr-6 pb-8 w-full lg:w-1/2" input-class="form-input bg-grey-lighter" label="Factura" disabled/>
                    <text-input v-model="form.total" class="pr-6 pb-8 w-full lg:w-1/2" input-class="form-input bg-grey-lighter" label="Total" disabled/>
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <!--<button v-if="!organization.deleted_at" class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">Eliminar organizacion</button>
                    <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Actualizar organizacion</loading-button>-->
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
      bill: Object
    },
    remember: 'form',
    data() {
      return {
        sending: false,
        form: {
          client: this.bill.client,
          id: this.bill.id,
          total: this.bill.total
        },
      }
    },
    methods: {

    },
  }
</script>