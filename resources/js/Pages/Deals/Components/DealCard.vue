<script setup>
defineProps({
    deal: Object,
})
</script>

<template>
    <div class="bg-white p-3 rounded shadow mb-2">
        <div class="font-medium">
            {{ deal.title }}
        </div>

        <div class="text-sm text-gray-500">
            {{ deal.entity?.name ?? 'Sem entidade' }}
        </div>

        <div class="text-sm mt-1">
            € {{ deal.value ?? '-' }}
        </div>
    </div>
</template>
