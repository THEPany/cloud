<template>
    <layout :title="'Factura ' + bill.id">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-green-light hover:text-green-dark" :href="route('invoice.bills.index', organization.slug)">Facturas</inertia-link>
            <span class="text-green-light font-medium">/</span>
            Factura {{ bill.id }}
        </h1>
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

        },
      }
    },
    methods: {

    },
  }
</script>