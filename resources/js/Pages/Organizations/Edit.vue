<template>
    <layout :title="form.name">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('organizations.index')">Organizations</inertia-link>
            <span class="text-indigo-light font-medium">/</span>
            {{ form.name }}
        </h1>
        <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore">
            This organization has been deleted.
        </trashed-message>
        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.name" :errors="errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Nombre"/>
                    <text-input v-model="form.email" :errors="errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Correo electrónico"/>
                    <text-input v-model="form.phone" :errors="errors.phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Teléfono"/>
                    <text-input v-model="form.address" :errors="errors.address" class="pr-6 pb-8 w-full lg:w-1/2" label="Dirección"/>
                    <text-input v-model="form.city" :errors="errors.city" class="pr-6 pb-8 w-full lg:w-1/2" label="Ciudad"/>
                    <text-input v-model="form.region" :errors="errors.region" class="pr-6 pb-8 w-full lg:w-1/2" label="Provincia/Estado"/>
                    <select-input v-model="form.country" :errors="errors.country" class="pr-6 pb-8 w-full lg:w-1/2" label="País">
                        <option :value="null" />
                        <option value="US">Estados Unidos</option>
                        <option value="DO">Rep. Dominicana</option>
                    </select-input>
                    <text-input v-model="form.postal_code" :errors="errors.postal_code" class="pr-6 pb-8 w-full lg:w-1/2" label="Postal code" />
                </div>
                <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <button v-if="!organization.deleted_at" class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">Eliminar organizacion</button>
                    <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Actualizar organizacion</loading-button>
                </div>
            </form>
        </div>

        <div class="flex flex-wrap mt-12">
            <div class="w-1/4 p-0 m-0">
                <h2 class="font-bold text-2xl">Usuarios</h2>
            </div>
            <div class="w-3/4 p-0 m-0">
                <multiselect v-model="value" :options="users" :custom-label="customLabel"
                             :searchable="true" placeholder="Buscar colaborador" open-direction="bottom"
                             :limit="5" @input="selectColaborator" ></multiselect>
            </div>
        </div>

        <div class="mt-6 bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">Nombre</th>
                    <th class="px-6 pt-6 pb-4">Correo electrónico</th>
                    <th class="px-6 pt-6 pb-4" colspan="2">Acciones</th>
                </tr>
                <tr v-for="user in organization.users" :key="user.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
                    <td class="border-t">
                        <div class="px-6 py-4 flex items-center focus:text-indigo">
                            {{ user.name }}
                            <icon v-if="user.id === organization.user_id" name="star" class="flex-no-shrink w-3 h-3 fill-grey ml-2" />
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="px-6 py-4 flex items-center" tabindex="-1">
                            {{ user.email }}
                        </div>
                    </td>
                    <td class="border-t">
                        <div class="px-6 py-4 flex items-center" tabindex="-1">
                            <icon name="trash" class="flex-no-shrink w-4 h-4 fill-red ml-2" />
                        </div>
                    </td>
                </tr>
                <tr v-if="organization.users.length === 0">
                    <td class="border-t px-6 py-4" colspan="4">No se encontraron usuarios.</td>
                </tr>
            </table>
        </div>
    </layout>
</template>

<script>
    import Icon from '@/Partials/Icon'
    import Layout from '@/Partials/Layout'
    import LoadingButton from '@/Partials/LoadingButton'
    import SelectInput from '@/Partials/SelectInput'
    import TextInput from '@/Partials/TextInput'
    import TrashedMessage from '@/Partials/TrashedMessage'
    import Multiselect from 'vue-multiselect'

    export default {
        components: {
            Icon,
            Layout,
            LoadingButton,
            SelectInput,
            TextInput,
            TrashedMessage,
            Multiselect,
        },
        props: {
            organization: Object,
            users: Array,
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        remember: 'form',
        data() {
            return {
                sending: false,
                form: {
                    name: this.organization.name,
                    email: this.organization.email,
                    phone: this.organization.phone,
                    address: this.organization.address,
                    city: this.organization.city,
                    region: this.organization.region,
                    country: this.organization.country,
                    postal_code: this.organization.postal_code,
                },
                value: '',
            }
        },
        methods: {
            submit() {
                this.sending = true
                this.$inertia.put(this.route('organizations.update', this.organization.id), this.form)
                    .then(() => this.sending = false)
            },
            destroy() {
                if (confirm('¿Estás seguro de que quieres eliminar esta organización?')) {
                    this.$inertia.delete(this.route('organizations.destroy', this.organization.id))
                }
            },
            restore() {
                if (confirm('¿Estás seguro de que quieres restaurar esta organización?')) {
                    this.$inertia.put(this.route('organizations.restore', this.organization.id))
                }
            },
            customLabel ({ name, email }) {
                return `${name} – ${email}`
            },
            selectColaborator() {
                if (confirm('¿Desea enviar una invitación a este usuario para que sea parte de su organización?')) {
                    this.$inertia.post(this.route('organizations.send.invitation', {'organization': this.organization.id, 'user': this.value.id}), this.value);
                }
                this.value = '';
            }
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>