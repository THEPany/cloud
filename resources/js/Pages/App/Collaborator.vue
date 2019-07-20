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

            <div class="bg-white rounded shadow overflow-x-auto mt-24">
                <h1 class="font-bold text-3xl px-4 py-2 m-2">Colaboradores</h1>
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">Nombre</th>
                        <th class="px-6 pt-6 pb-4" colspan="2">Email</th>
                    </tr>
                    <tr v-for="contributor in contributors.data" :key="contributor.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
                        <td class="border-t">
                            <inertia-link :href="route('apps.collaborator.permissions', {'slug':organization.slug, 'user':contributor})" class="px-6 py-4 flex items-center focus:text-indigo">
                                {{ contributor.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link :href="route('apps.collaborator.permissions', {'slug':organization.slug, 'user':contributor})" class="px-6 py-4 flex items-center" tabindex="-1">
                                {{ contributor.email }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px">
                            <inertia-link :href="route('apps.collaborator.permissions', {'slug':organization.slug, 'user':contributor})" class="px-4 flex items-center" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
                            </inertia-link>
                        </td>
                    </tr>
                </table>
            </div>
            <pagination :links="contributors.links" />
        </div>
    </main>
</template>

<script>
  import Logo from '@/Partials/Logo'
  import Icon from '@/Partials/Icon'
  import Pagination from '@/Partials/Pagination'

  export default {
    components: {
      Logo,
      Icon,
      Pagination
    },
    props: {
      organization: Object,
      contributors: Object,
    },
  }
</script>