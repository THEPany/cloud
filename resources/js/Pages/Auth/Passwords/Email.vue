<template>
    <div class="p-6 bg-indigo-darker min-h-screen flex justify-center items-center">
        <div class="w-full max-w-sm">
            <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
            <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
                <div class="px-10 py-12">
                    <div v-show="message" class="bg-green-lightest border border-green-light text-green-dark text-center px-4 py-3 mb-2 rounded relative" role="alert">
                        <span class="block sm:inline">Hemos enviado un enlace de restablecimiento de contraseña a su <strong class="font-bold">correo electrónico</strong>.</span>
                    </div>
                    <h1 class="text-center font-bold text-3xl">Reset Password</h1>
                    <div class="mx-auto mt-6 w-24 border-b-2" />
                    <text-input v-model="form.email" :errors="$page.errors.email" class="mt-10" label="Email" type="email" autofocus autocapitalize="off" />
                </div>
                <div class="px-10 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-between items-center">
                    <loading-button :loading="sending" class="btn-indigo" type="submit">Send Password Reset Link</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import LoadingButton from '@/Partials/LoadingButton'
    import Logo from '@/Partials/Logo'
    import TextInput from '@/Partials/TextInput'

    export default {
        components: {
            LoadingButton,
            Logo,
            TextInput,
        },
        data() {
            return {
                sending: false,
                message: false,
                form: {
                    email: null,
                },
            }
        },
        mounted() {
            document.title = `Reset Password | ${this.$page.app.name}`
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.post(this.route('password.email'), {
                    email: this.form.email,
                }).then(() => {
                    this.sending = false;
                    this.message = true;
                    this.form.email = null;
                })
            },
        },
    }
</script>