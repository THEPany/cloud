<template>
    <layout title="Home">
        <h1 class="mb-8 font-bold text-3xl">Permisos de colaboradores</h1>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">Nombre</th>
                    <th class="px-6 pt-6 pb-4" colspan="2">Correo electrónicos</th>
                </tr>
                <tr v-for="user in contributors.data" :key="user.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
                    <td class="border-t">
                        <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('setting.permissions.show', {'organization':organization.slug, 'user':user.id})">
                            {{ user.name }}
                        </inertia-link>
                    </td>
                    <td class="border-t">
                        <inertia-link class="px-6 py-4 flex items-center" :href="route('setting.permissions.show', {'organization':organization.slug, 'user':user.id})" tabindex="-1">
                            {{ user.email }}
                        </inertia-link>
                    </td>
                    <td class="border-t w-px">
                        <inertia-link class="px-4 flex items-center" :href="route('setting.permissions.show', {'organization':organization.slug, 'user':user.id})" tabindex="-1">
                            <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
                        </inertia-link>
                    </td>
                </tr>
                <tr v-if="contributors.data.length === 0">
                    <td class="border-t px-6 py-4" colspan="3">
                        <div class="text-center flex flex-wrap justify-center">
                            <span class="w-full pt-4 pb-4 text-grey-dark">No se han encontrado Colaboradores.</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <pagination :links="contributors.links" />
    </layout>
</template>

<script>
  import Icon from '@/Partials/Icon'
  import Pagination from '@/Partials/Pagination'
  import Layout from '@/Pages/Setting/Partials/Layout'

  export default {
    components: {
      Icon,
      Pagination,
      Layout
    },
    props: {
      organization: Object,
      contributors: Object,
    },
  }
</script>
