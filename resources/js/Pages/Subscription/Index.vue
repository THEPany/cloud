<template>
    <layout title="Subscription">
        <h1 class="mb-8 font-bold text-3xl">Suscripción</h1>

        <div v-if="!isSubscribed" class="bg-white rounded shadow overflow-hidden max-w-lg mb-12">
            <form @submit.prevent="create">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <h2 class="text-grey-dark w-full pb-4">Suscribirse a la membresía</h2>
                    <select-input v-model="form.plan" :errors="errors.plan" class="pr-6 pb-4 w-full" label="Plan">
                        <option :value="null" />
                        <option :value="plan.id" v-for="plan in plans">{{ plan.name + ' RD$ ' + plan.price }}</option>
                    </select-input>

                    <div v-show="form.plan" class="pr-6 w-full" id="dropin-container"></div>
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                    <loading-button :loading="sending" class="flex items-center btn-indigo" type="submit">Pagar</loading-button>
                </div>
            </form>
        </div>

        <div v-if="isSubscribed" class="bg-white rounded shadow overflow-hidden max-w-lg mb-12">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <h2 class="text-grey-dark w-full">Cambiar plan</h2>

                <p class="pt-8 w-full text-black font-bold">{{ card.card_brand }}  **** **** *** {{ card.card_last_four }}</p>

                <select-input v-model="form.plan" :errors="errors.plan" class="pr-6 pb-8 pt-8 w-full" label="Actualizar Plan">
                    <option :value="plan.id" v-for="plan in plans">{{ plan.name + ' RD$ ' + plan.price }}</option>
                </select-input>
            </div>
            <form @submit.prevent="update">
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <button class="text-red hover:underline" tabindex="-1" type="button">Cancelar suscripción</button>
                    <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Cambiar plan</loading-button>
                </div>
            </form>
        </div>

        <div v-if="isSubscribed" class="bg-white rounded shadow overflow-hidden max-w-lg">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <h2 class="text-grey-dark w-full pb-4">Cambiar método de pago</h2>
                <div class="pr-6 w-full" id="dropin-container"></div>
            </div>
            <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                <form @submit.prevent="updateCard">
                    <loading-button :loading="sending" class="flex items-center btn-indigo" type="submit">Actualizar Metodo de pago</loading-button>
                </form>
            </div>
        </div>

    </layout>
</template>

<script>
    import Layout from '@/Partials/Layout'
    import LoadingButton from '@/Partials/LoadingButton'
    import SelectInput from '@/Partials/SelectInput'
    import TextInput from '@/Partials/TextInput'

    let dropin = require('braintree-web-drop-in');

    export default {
        components: {
            Layout,
            LoadingButton,
            SelectInput,
            TextInput,
        },
        props: {
            token: String,
            isSubscribed: Boolean,
            plans: Array,
            plan: Object,
            card: Object,
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                braintree: {
                    dropinErr: null,
                    dropin: null,
                },
                form: {
                    plan: this.plan ? this.plan.id : null,
                    payment_method_nonce: null
                },
            }
        },
        created() {
            dropin.create({
                authorization: this.token,
                container: "#dropin-container",
                // paypal: { flow: 'vault' }
            },(dropinErr, dropin) => { this.braintree.dropinErr = dropinErr; this.braintree.dropin = dropin; });
        },
        methods: {
            create() {
                this.sending = true;
                this.braintree.dropin.requestPaymentMethod((requestPaymentMethodErr, payload) => {
                    if(requestPaymentMethodErr) {
                        console.log(requestPaymentMethodErr);
                    }
                    this.form.payment_method_nonce = payload.nonce;
                    this.$inertia.post(this.route('subscriptions.store'), this.form)
                        .then(() => this.sending = false)
                });
            },
            updateCard() {
                this.sending = true;
                this.braintree.dropin.requestPaymentMethod((requestPaymentMethodErr, payload) => {
                    if(requestPaymentMethodErr) {
                        console.log(requestPaymentMethodErr);
                    }
                    this.form.payment_method_nonce = payload.nonce;
                    this.$inertia.post(this.route('subscriptions.card'), this.form)
                        .then(() => this.sending = false)
                });
            },
            update() {
                this.sending = true;
                this.$inertia.put(this.route('subscriptions.update'), this.form)
                    .then(() => this.sending = false)
            },
        }
    }
</script>