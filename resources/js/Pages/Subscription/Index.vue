<template>
    <layout title="Subscription">
        <h1 class="mb-8 font-bold text-3xl">Suscripción</h1>

        <div v-if="isCancel" class="bg-white rounded shadow overflow-hidden max-w-lg mb-12">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap" v-if="isTrial">
                <p class="pb-2">Los beneficios de su suscripción <span class="capitalize font-bold">{{ plan.braintree_plan }}</span>, continuarán hasta que finalice su período de prueba actual <span class="font-bold">{{ isTrial }}</span>.</p>
            </div>
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap" v-else>
                <p class="pb-2">Los beneficios de su suscripción <span class="capitalize font-bold">{{ plan.braintree_plan }}</span>, continuarán hasta que finalice su período de facturación actual <span class="font-bold">{{ isCancel }}</span>.</p>
                <p class="pb-2">Puede reanudar su suscripción sin costo adicional hasta el final del período de facturación.</p>
            </div>
            <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                <button :disabled="isTrial || endSubscription" :class="{'opacity-50 cursor-not-allowed hover:bg-indigo-dark':isTrial || endSubscription}" @click="cancelNowSubscription"  class="text-red hover:underline" tabindex="-1" type="button">Eliminar suscripción ahora</button>
                <button v-if="!endSubscription" :disabled="isTrial" :class="{'opacity-50 cursor-not-allowed hover:bg-indigo-dark':isTrial}" @click="resumeSubscription" :loading="sending" class="btn-indigo ml-auto" type="submit">Reanudar</button>
                <button v-else @click="newSubscription" :loading="sending" class="btn-indigo ml-auto" type="submit">Nueva suscripción</button>
            </div>
        </div>
        <div v-else>
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
                    <div v-if="form.plan" class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
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
                        <button @click="cancelSubscription" class="text-red hover:underline" tabindex="-1" type="button">Cancelar suscripción</button>
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
            endSubscription: Boolean,
            isCancel: String,
            isTrial: String,
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
            if (!this.isCancel) {
                this.createDropin();
            }
        },
        methods: {
            create() {
                this.sending = true;
                this.braintree.dropin.requestPaymentMethod((requestPaymentMethodErr, payload) => {
                    if(requestPaymentMethodErr) {
                        /*console.log(requestPaymentMethodErr);*/
                        this.sending = false;
                    }
                    this.form.payment_method_nonce = payload.nonce;
                    this.$inertia.post(this.route('subscriptions.store'), this.form)
                        .then(() => { this.sending = false; this.createDropin(); })
                });
            },
            updateCard() {
                this.sending = true;
                this.braintree.dropin.requestPaymentMethod((requestPaymentMethodErr, payload) => {
                    if(requestPaymentMethodErr) {
                       /* console.log(requestPaymentMethodErr);*/
                        this.sending = false
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
            cancelSubscription() {
                if (confirm('¿Seguro que quieres cancelar la suscripción?')) {
                    this.$inertia.visit(this.route('subscriptions.cancel'));
                }
            },
            cancelNowSubscription() {
                if (confirm('¿Está seguro de que desea eliminar por completo la suscripción?')) {
                    this.$inertia.post(this.route('subscriptions.cancelNowSubscription'), {});
                }
            },
            resumeSubscription() {
                this.$inertia.post(this.route('subscriptions.resumeSubscription'), {});
            },
            newSubscription() {
                this.isCancel = false;
                this.isSubscribed = false;
                this.createDropin();
            },
            createDropin() {
                dropin.create({
                    authorization: this.token,
                    container: "#dropin-container",
                    // paypal: { flow: 'vault' }
                },(dropinErr, dropin) => { this.braintree.dropinErr = dropinErr; this.braintree.dropin = dropin; });
            }
        }
    }
</script>