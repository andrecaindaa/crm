<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { onMounted, ref } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    kpis: Object,
    pipeline: Object,
    inactiveList: Array,
    topProducts: Array,
    highRiskDeals: Array,
})

const pipelineChart = ref(null)
const revenueChart = ref(null)

onMounted(() => {

    new Chart(pipelineChart.value, {
        type: 'bar',
        data: {
            labels: Object.keys(props.pipeline),
            datasets: [{
                label: 'Negócios por estágio',
                data: Object.values(props.pipeline),
            }]
        }
    })

    new Chart(revenueChart.value, {
        type: 'doughnut',
        data: {
            labels: ['Prevista', 'Fechada'],
            datasets: [{
                data: [
                    props.kpis.expected_revenue,
                    props.kpis.closed_revenue
                ]
            }]
        }
    })
})
</script>

<template>
<CrmLayout>


    <div class="grid grid-cols-2 gap-10 mb-12">
    <canvas ref="pipelineChart"></canvas>
    <canvas ref="revenueChart"></canvas>
</div>


<h1 class="text-2xl font-bold mb-6">Dashboard CRM</h1>

<!-- KPIs -->
<div class="grid grid-cols-4 gap-6 mb-10">
    <div class="bg-white shadow rounded p-4">
        <p class="text-sm text-gray-500">Total Negócios</p>
        <p class="text-2xl font-bold">{{ kpis.total_deals }}</p>
    </div>

    <div class="bg-red-50 shadow rounded p-4">
        <p class="text-sm text-gray-500">Negócios Inativos</p>
        <p class="text-2xl font-bold text-red-600">
            {{ kpis.inactive_deals }}
        </p>
    </div>

    <div class="bg-yellow-50 shadow rounded p-4">
        <p class="text-sm text-gray-500">Receita Prevista</p>
        <p class="text-2xl font-bold">
            {{ Number(kpis.expected_revenue).toFixed(2) }}€
        </p>
    </div>

    <div class="bg-green-50 shadow rounded p-4">
        <p class="text-sm text-gray-500">Receita Fechada</p>
        <p class="text-2xl font-bold text-green-600">
            {{ Number(kpis.closed_revenue).toFixed(2) }}€
        </p>
    </div>
</div>

<!-- Pipeline -->
<div class="mb-10">
    <h2 class="font-semibold mb-3">Pipeline por Estágio</h2>

    <div class="flex gap-4 flex-wrap">
        <div
            v-for="(total, stage) in pipeline"
            :key="stage"
            class="border rounded p-3 text-sm"
        >
            {{ stage }}: <strong>{{ total }}</strong>
        </div>
    </div>
</div>

<!-- Inativos -->
<div class="mb-10">
    <h2 class="font-semibold mb-3 text-red-600">
        ⚠️ Negócios Inativos Prioritários
    </h2>

    <ul class="space-y-2">
        <li
            v-for="deal in inactiveList"
            :key="deal.id"
            class="border rounded p-3 text-sm"
        >
            {{ deal.title }}
            · {{ deal.value }}€
            · {{ deal.inactive_days }} dias
        </li>
    </ul>
</div>

<!-- Top Produtos -->
<div>
    <h2 class="font-semibold mb-3">Top Produtos</h2>

    <ul class="space-y-2">
        <li
            v-for="product in topProducts"
            :key="product.id"
            class="border rounded p-3 text-sm"
        >
            {{ product.name }}
            · {{ Number(product.revenue).toFixed(2) }}€
        </li>
    </ul>
</div>

<div class="mt-10">
    <h2 class="font-semibold text-red-600 mb-3">
        🔥 Negócios de Alto Risco
    </h2>

    <ul class="space-y-2">
        <li v-for="deal in highRiskDeals" :key="deal.id"
            class="border rounded p-3 text-sm">
            {{ deal.title }}
            · Score: {{ deal.risk_score }}/100
        </li>
    </ul>
</div>


</CrmLayout>
</template>
