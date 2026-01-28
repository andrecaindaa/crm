<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import DealCard from './Components/DealCard.vue'
import { router } from '@inertiajs/vue3'

defineProps({
    stages: Array,
    deals: Object,
})

function moveDeal(dealId, stage) {
    router.patch(`/deals/${dealId}/stage`, { stage })
}
</script>

<template>
    <CrmLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Negócios</h1>

            <a
                href="/deals/create"
                class="px-4 py-2 bg-black text-white rounded"
            >
                Novo Negócio
            </a>
        </div>

        <div class="grid grid-cols-6 gap-4 overflow-x-auto">
            <div
                v-for="stage in stages"
                :key="stage"
                class="bg-gray-100 p-3 rounded min-w-[220px]"
            >
                <h2 class="font-semibold mb-3 capitalize">
                    {{ stage.replace('_', ' ') }}
                </h2>

                <div
                    v-for="deal in deals[stage] ?? []"
                    :key="deal.id"
                    class="cursor-pointer"
                    @dblclick="moveDeal(deal.id, stage)"
                >
                    <DealCard :deal="deal" />
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-4">
            (Duplo clique num negócio para testar mudança de etapa)
        </p>
    </CrmLayout>
</template>
