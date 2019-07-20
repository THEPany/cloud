<template>
    <main>
        <div class="mx-auto px-4 md:px-24">
            <div class="p-8 -mr-6 -mb-8">
                <div class="text-center md:flex md:justify-between md:items-center">
                    <inertia-link  :href="route('home.index')">
                        <logo class="fill-grey" width="120" height="28" />
                    </inertia-link>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl xl:text-4xl font-light leading-tight">
                        {{ organization.name }}
                    </h1>
                </div>
            </div>

            <h1 class="font-bold text-3xl px-4 py-2 m-2">Asignar permisos a  {{ user.name }}</h1>
            <div class="bg-white rounded shadow overflow-hidden max-w-lg">
                <form @submit.prevent="submit">
                    <div class="p-8 -mr-6 -mb-8 flex flex-wrap" v-for="(permission, index) in permissions">
                        <h3 class="w-full">{{ index }}</h3>
                        <label class="mt-6 select-none flex items-center w-full" :for="object.id" v-for="object in permission">
                            <input :id="object.id" v-model="form.permissions" :value="object.id" class="mr-1" type="checkbox">
                            <span class="text-sm">{{ object.title }}</span>
                        </label>
                    </div>
                    <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
                        <loading-button :loading="sending" class="btn-green" type="submit">Actualizar</loading-button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</template>


<script>
  import Logo from '@/Partials/Logo'
  import Icon from '@/Partials/Icon'
  import LoadingButton from '@/Partials/LoadingButton'

  export default {
    components: {
      Logo,
      Icon,
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
        this.$inertia.post(this.route('apps.collaborator.permissions.store', {'slug':this.organization.slug, 'user':this.user.id}), this.form)
          .then(() => this.sending = false)
      },
    },
  }
</script>