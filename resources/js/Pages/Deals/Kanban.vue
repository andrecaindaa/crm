<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    deals: Object,
    stages: Object,
})

function onDrop(dealId, stage) {
    router.patch(`/deals/${dealId}/stage`, { stage })
}
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-6">Pipeline de Negócios</h1>

        <div class="grid grid-cols-6 gap-4">
            <div
                v-for="(label, stage) in stages"
                :key="stage"
                class="bg-gray-100 rounded p-3"
                @dragover.prevent
                @drop="e => onDrop(e.dataTransfer.getData('deal'), stage)"
            >
                <h2 class="font-semibold mb-2">
                    {{ label }}
                </h2>

                <p class="text-sm mb-3 text-gray-600">
                    Total:
                    {{
                        (deals[stage] ?? []).reduce(
                            (sum, d) => sum + (parseFloat(d.value) || 0),
                            0
                        )
                    }} €
                </p>

                <div class="space-y-2">
                    <div
                        v-for="deal in deals[stage]"
                        :key="deal.id"
                        draggable
                        @dragstart="e => e.dataTransfer.setData('deal', deal.id)"
                        class="bg-white p-2 rounded shadow cursor-move"
                    >
                        <strong>{{ deal.title }}</strong>
                        <p class="text-sm text-gray-500">
                            {{ deal.value ?? '—' }} €
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </CrmLayout>
</template>
