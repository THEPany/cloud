<template>
    <layout title="Permisos">
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('setting.permissions', organization.slug)">Colaboradores</inertia-link>
            <span class="text-indigo-light font-medium">/</span>
            Asignar permisos a  {{ user.name }}
        </h1>

        <div class="bg-white rounded shadow overflow-hidden max-w-lg">
            <form @submit.prevent="submit">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap" v-for="(permission, index) in permissions">
                    <h3 class="w-full">{{ index }}</h3>
                    <label class="mt-6 select-none flex items-center w-full" :for="object.id" v-for="object in permission">
                        <input :id="object.id" v-model="form.permissions" :value="object.id" class="mr-1" type="checkbox">
                        <span class="text-sm">{{ object.title }}</span>
                    </label>
                </div>
                <div class="mt-4 px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
                    <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Actualizar</loading-button>
                </div>
            </form>
        </div>
    </layout>
</template>

<script>
  import Icon from '@/Partials/Icon'
  import Layout from '@/Pages/Cloud/Partials/Layout'
  import LoadingButton from '@/Partials/LoadingButton'

  export default {
    components: {
      Icon,
      Layout,
      LoadingButton
    },
    name: 'Permission',
    props: {
      organization: Object,
      permissions: Object,
      user: Object,
    },
    data() {
      return {
        sending: false,
        form: {
          permissions: this.user.permissions.map(permission => permission.id),
        }
      }
    },
    methods: {
      submit() {
        this.sending = true
        this.$inertia.post(this.route('setting.permissions.store', {'slug':this.organization.slug, 'user':this.user.id}), this.form)
          .then(() => this.sending = false)
      },
    },
  }
</script>
