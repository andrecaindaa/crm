<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    people: Array
})
</script>

<template>
    <CrmLayout>
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Pessoas</h1>

            <Link href="/people/create" class="px-4 py-2 bg-black text-white rounded">
                Nova Pessoa
            </Link>
        </div>

        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="border-b text-left">
                    <th class="p-3">Nome</th>
                    <th class="p-3">Entidade</th>
                    <th class="p-3">Email</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="person in people" :key="person.id" class="border-b">
                    <td class="p-3">{{ person.name }}</td>
                    <td class="p-3">{{ person.entity?.name ?? '-' }}</td>
                    <td class="p-3">{{ person.email }}</td>
                    <td class="p-3 text-right">
                        <Link :href="`/people/${person.id}/edit`">Editar</Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </CrmLayout>
</template>
