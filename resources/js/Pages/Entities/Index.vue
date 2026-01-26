<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    entities: Array
})
</script>

<template>
    <CrmLayout>
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Entidades</h1>

            <Link
                href="/entities/create"
                class="px-4 py-2 bg-black text-white rounded"
            >
                Nova Entidade
            </Link>
        </div>

        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-3">Nome</th>
                    <th class="p-3">VAT</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3"></th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="entity in entities" :key="entity.id" class="border-b">
                    <td class="p-3">{{ entity.name }}</td>
                    <td class="p-3">{{ entity.vat }}</td>
                    <td class="p-3">{{ entity.status }}</td>
                    <td class="p-3 text-right space-x-2">
                        <Link :href="`/entities/${entity.id}/edit`">Editar</Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </CrmLayout>
</template>
