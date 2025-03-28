<template>
    <AppLayout title="Gestion">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestion des points
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="text-xl font-semibold">Points Géographiques</h5>
                            <a :href=" route('addPoint') " class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
                                <i class="fas fa-add mr-2"></i> Ajouter un point
                            </a>
                        </div>

                        <div class="mb-6">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Rechercher un point..."
                                @input="onSearch"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase">Nom</th>
                                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase">Longitude</th>
                                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase">Latitude</th>
                                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="point in points" :key="point.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 border-b border-gray-200">{{ point.name }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">{{ point.longitude }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">{{ point.latitude }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">
                                            <a :href="route('edit', point.id)" class="text-blue-500 hover:text-blue-700 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button @click="deletePoint(point.id)" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

export default {
    props: {
        points: Array,
        filters: Object,
    },
    setup(props) {
        const search = ref(props.filters.search || '');

        const addPoint = () => {
            router.get(route('addPoint')); 
        };

        const onSearch = () => {
            router.get(route('gestion'), { search: search.value }, { preserveState: true });
        };

        const deletePoint = (pointId) => {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce point ?')) {
                router.delete(route('delete', pointId));
            }
        };

        watch(search, (value) => {
            onSearch();
        });

        return {
            search,
            onSearch,
            deletePoint,
            addPoint,
        };
    },
};
</script>
