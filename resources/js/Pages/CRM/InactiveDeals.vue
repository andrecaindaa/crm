<script setup>
import { computed } from 'vue'
import CrmLayout from '@/Layouts/CrmLayout.vue'

const props = defineProps({
    deals: Array
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT')
}

const getAlertLevel = (days) => {
    if (days >= 10) return 'bg-red-100 text-red-800'
    if (days >= 7) return 'bg-orange-100 text-orange-800'
    return 'bg-yellow-100 text-yellow-800'
}
</script>

<template>
    <CrmLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold">🚨 Negócios Inativos</h1>
            <p class="text-gray-600">Negócios sem atividade há mais de 5 dias</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Negócio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estágio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Atividade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dias Inativo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsável</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="deal in deals" :key="deal.id">
                        <td class="px-6 py-4">
                            <a :href="`/deals/${deal.id}`" class="text-blue-600 hover:underline">
                                {{ deal.title }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ deal.stage }}</td>
                        <td class="px-6 py-4">{{ formatDate(deal.last_activity_at) }}</td>
                        <td class="px-6 py-4">
                            <span :class="getAlertLevel(deal.inactive_days)" class="px-2 py-1 rounded text-xs font-medium">
                                {{ deal.inactive_days }} dias
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ deal.owner?.name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </CrmLayout>
</template>
