<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { router } from '@inertiajs/vue3'

defineProps({
    deals: Object,
    stages: Object,
})

function onDrop(dealId, stage) {
    router.patch(`/deals/${dealId}/stage`, { stage })
}

function openDeal(dealId) {
    router.visit(`/deals/${dealId}`)
}
</script>

<template>
    <CrmLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pipeline de Negócios</h1>

            <a href="/deals/create" class="px-4 py-2 bg-black text-white rounded">
                Novo Negócio
            </a>
        </div>

        <div class="grid grid-cols-6 gap-4">
            <div
                v-for="(label, stage) in stages"
                :key="stage"
                class="bg-gray-100 rounded p-3"
                @dragover.prevent
                @drop="e => onDrop(e.dataTransfer.getData('deal'), stage)"
            >
                <h2 class="font-semibold mb-3">{{ label }}</h2>

                <div class="space-y-2">
                    <div
                        v-for="deal in deals[stage] ?? []"
                        :key="deal.id"
                        draggable
                        @dragstart="e => e.dataTransfer.setData('deal', deal.id)"
                        @click="openDeal(deal.id)"
                        class="bg-white p-3 rounded shadow cursor-pointer hover:bg-gray-50"
                    >
                        <strong>{{ deal.title }}</strong>
                        <p class="text-sm text-gray-500">
                            {{ deal.entity?.name ?? 'Sem entidade' }}
                        </p>
                        <p class="text-sm">
                            € {{ deal.value ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </CrmLayout>
</template>
