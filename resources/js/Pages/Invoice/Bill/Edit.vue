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
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.client" class="pr-6 pb-8 w-full" input-class="form-input bg-grey-lighter" label="Cliente" disabled/>
                    <text-input v-model="form.id" class="pr-6 pb-8 w-full" input-class="form-input bg-grey-lighter" label="Factura" disabled/>
                    <text-input v-model="form.due_amount" class="pr-6 pb-8 w-full" input-class="form-input bg-grey-lighter" label="Monto pendiente" disabled/>
                    <text-input type="number" v-model="form.paid_out" :errors="$page.errors.paid_out"
                                class="pr-6 pb-8 w-full" :input-class="canPaidBill" label="Monto pendiente" :disabled="disabled"/>
                </div>
                <div v-if="!disabled" class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <loading-button :loading="sending" class="btn-green ml-auto" type="submit">Abonar deuda</loading-button>
                </div>
            </form>
        </div>


        <div class="mt-12">
            <iframe class="w-full h-screen" :src="route('invoice.bills.show', {'slug':organization.slug,'bill':bill})"></iframe>
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
          due_amount: this.bill.due_amount,
          paid_out: ''
        },
      }
    },
    methods: {
      submit() {
        if (this.paid_out > this.due_amount) {
          this.paid_out = this.due_amount;
        }
        this.sending = true
        this.$inertia.put(this.route('invoice.bills.update', {'slug':this.slug,'bill':this.bill}), this.form)
          .then(() => {
            this.sending = false;
          })
      },
    },
    computed: {
      canPaidBill() {
        if (this.bill.status === 'EN PROCESO') {
          return 'form-input';
        }
        return 'form-input bg-grey-lighter'
      },
      disabled() {
        if (this.bill.status === 'EN PROCESO' || this.due_amount > 0) {
          return false;
        }
        return true;
      }
    }
  }
</script>