<template>
    <layout title="Home">
        <h1 class="mb-8 font-bold text-3xl">Dashboard</h1>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <tr class="text-left font-bold">
                    <th class="px-6 pt-6 pb-4">Organización</th>
                    <th class="px-6 pt-6 pb-4">Correo electrónico</th>
                    <th class="px-6 pt-6 pb-4" colspan="2">Propietario</th>
                </tr>
                <tr v-for="organization in organizations.data" :key="organization.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
                    <td class="border-t">
                        <a class="px-6 py-4 flex items-center focus:text-indigo" :href="route('apps.index', organization.slug)">
                            {{ organization.name }}
                        </a>
                    </td>
                    <td class="border-t">
                        <a class="px-6 py-4 flex items-center" :href="route('apps.index', organization.slug)" tabindex="-1">
                            {{ organization.email }}
                        </a>
                    </td>
                    <td class="border-t">
                        <a class="px-6 py-4 flex items-center" :href="route('apps.index', organization.slug)" tabindex="-1">
                            {{ organization.user.name }}
                        </a>
                    </td>
                    <td class="border-t w-px">
                        <a class="px-4 flex items-center" tabindex="-1">
                            <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
                        </a>
                    </td>
                </tr>
                <tr v-if="organizations.data.length === 0">
                    <td class="border-t px-6 py-4" colspan="4">No se han encontrado organizaciones.</td>
                </tr>
            </table>
        </div>
        <pagination :links="organizations.links" />
    </layout>
</template>

<script>
    import _ from 'lodash'
    import Icon from '@/Partials/Icon'
    import Layout from '@/Partials/Layout'
    import Pagination from '@/Partials/Pagination'
    import SearchFilter from '@/Partials/SearchFilter'
    export default {
        components: {
            Icon,
            Layout,
            Pagination,
            SearchFilter,
        },
        props: {
            organizations: Object,
        },
        data() {
            return {
                form: {
                    search: this.filters.search,
                    trashed: this.filters.trashed,
                },
            }
        },
        watch: {
            form: {
                handler: _.throttle(function() {
                    let query = _.pickBy(this.form)
                    this.$inertia.replace(this.route('organizations.index', Object.keys(query).length ? query : { '': '' }))
                }, 150),
                deep: true,
            },
        },
        methods: {
            reset() {
                this.form = _.mapValues(this.form, () => null)
            },
        },
    }
</script>