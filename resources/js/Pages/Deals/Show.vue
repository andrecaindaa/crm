<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import BaseModal from '@/Components/BaseModal.vue'
import { ref, onMounted } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    deal: Object,
    timeline: Array,
    activeFollowUp: Object,
    productsList: Array,
})

const user = usePage().props.auth.user

/*
|--------------------------------------------------------------------------
| PRODUTOS - MODAL
|--------------------------------------------------------------------------
*/

const showProductModal = ref(false)

const productForm = useForm({
    product_id: '',
    quantity: 1,
    unit_price: 0,
})

// Watch para atualizar preço quando produto selecionado
const updateProductPrice = (id) => {
    const selected = props.productsList?.find(p => Number(p.id) === Number(id))
    if (selected) {
        productForm.unit_price = selected.price
    }
}

// Função para calcular total
const calculatedTotal = () => {
    const quantity = Number(productForm.quantity) || 0
    const price = Number(productForm.unit_price) || 0
    return (quantity * price).toFixed(2)
}

function attachProduct() {
    productForm.post(`/deals/${props.deal.id}/products`, {
        preserveScroll: true,
        onSuccess: () => {
            showProductModal.value = false
            productForm.reset()
            router.reload({ only: ['deal', 'timeline'] })
        }
    })
}

/*
|--------------------------------------------------------------------------
| FILTROS TIMELINE
|--------------------------------------------------------------------------
*/

const timelineFilter = ref('all')

const filteredTimeline = () => {
    if (!props.timeline) return []

    if (timelineFilter.value === 'all') {
        return props.timeline
    }

    if (timelineFilter.value === 'emails') {
        return props.timeline.filter(item =>
            ['proposal_sent', 'follow_up'].includes(item.type)
        )
    }

    return props.timeline.filter(item =>
        item.type === timelineFilter.value
    )
}

const filterClass = "px-3 py-1 border rounded text-gray-600 hover:bg-gray-100"
const activeFilterClass = "px-3 py-1 border rounded bg-black text-white"

/*
|--------------------------------------------------------------------------
| MODAL TIMELINE
|--------------------------------------------------------------------------
*/

const selectedTimelineItem = ref(null)
const showTimelineModal = ref(false)

function openTimelineModal(item) {
    selectedTimelineItem.value = item
    showTimelineModal.value = true
}

function closeTimelineModal() {
    showTimelineModal.value = false
    selectedTimelineItem.value = null
}

/*
|--------------------------------------------------------------------------
| FOLLOW-UP
|--------------------------------------------------------------------------
*/

const followUpTemplates = ref([])

const followUpForm = useForm({
    body: '',
    interval_days: 2,
})

onMounted(async () => {
    try {
        const { data } = await axios.get('/follow-ups/templates')
        followUpTemplates.value = data
    } catch (error) {
        console.error('Erro ao carregar templates:', error)
    }
})

function sendFollowUp() {
    followUpForm.post(`/deals/${props.deal.id}/follow-ups`, {
        preserveScroll: true,
        onSuccess: () => {
            followUpForm.body = ''
            router.reload({ only: ['timeline', 'activeFollowUp'] })
        },
    })
}

function cancelFollowUp() {
    if (!props.activeFollowUp) return

    followUpForm.patch(
        `/follow-ups/${props.activeFollowUp.id}/cancel`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['timeline', 'activeFollowUp'] })
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| PROPOSTAS
|--------------------------------------------------------------------------
*/

const uploadForm = useForm({
    proposal: null,
})

function uploadProposal() {
    uploadForm.post(`/deals/${props.deal.id}/proposals`, {
        forceFormData: true,
        onSuccess: () => {
            router.reload({ only: ['deal', 'timeline'] })
        }
    })
}

const emailBody = ref(
`Olá,

Segue em anexo a proposta conforme combinado.

Fico disponível para qualquer questão.

Cumprimentos,
${user.name}`
)

const sendForm = useForm({
    body: emailBody.value,
})

function sendProposal(proposalId) {
    sendForm.body = emailBody.value

    sendForm.post(`/proposals/${proposalId}/send`, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['timeline'] })
        },
    })
}

/*
|--------------------------------------------------------------------------
| ATIVIDADES
|--------------------------------------------------------------------------
*/

const activityForm = useForm({
    type: 'note',
    description: '',
    due_at: null,
})

function createActivity() {
    activityForm.post(`/deals/${props.deal.id}/activities`, {
        preserveScroll: true,
        onSuccess: () => {
            activityForm.reset()
            router.reload({ only: ['timeline'] })
        },
    })
}

/*
|--------------------------------------------------------------------------
| FORMATAR DATA
|--------------------------------------------------------------------------
*/

function formatDate(dateString) {
    if (!dateString) return ''

    const date = new Date(dateString)
    return date.toLocaleString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
<CrmLayout>

<!-- Cabeçalho -->
<div class="mb-6">
    <h1 class="text-2xl font-bold">{{ deal?.title }}</h1>

    <p class="text-sm text-gray-600 mt-1">
        Valor: {{ deal?.value ?? '—' }} €
        · Estado: {{ deal?.stage }}
    </p>

    <p class="text-sm mt-1">
        <span v-if="deal?.entity">Entidade: {{ deal.entity.name }}</span>
        <span v-if="deal?.person"> · Pessoa: {{ deal.person.name }}</span>
    </p>
</div>

<!-- Upload de proposta -->
<section class="mb-8">
    <h2 class="font-semibold mb-2">Adicionar proposta</h2>

    <form @submit.prevent="uploadProposal" class="flex items-center gap-4">
        <input
            type="file"
            @change="e => uploadForm.proposal = e.target.files?.[0]"
            class="border rounded p-1"
        />

        <button
            class="px-4 py-2 bg-black text-white rounded disabled:opacity-50"
            :disabled="uploadForm.processing"
        >
            Upload
        </button>
    </form>
</section>

<!-- Lista de propostas -->
<section class="mb-8">
    <h2 class="font-semibold mb-3">Propostas</h2>

    <ul class="space-y-4">
        <li
            v-for="proposal in deal?.proposals || []"
            :key="proposal.id"
            class="border rounded p-4"
        >
            <div class="flex justify-between items-center">
                <span class="font-medium">
                    {{ proposal.original_name }}
                </span>

                <span
                    v-if="proposal.sent_at"
                    class="text-sm text-green-600"
                >
                    Enviado em {{ formatDate(proposal.sent_at) }}
                </span>
            </div>

            <div v-if="!proposal.sent_at" class="mt-3">
                <label class="block text-sm font-medium mb-1">
                    Texto do email
                </label>

                <textarea
                    v-model="emailBody"
                    rows="4"
                    class="w-full border rounded p-2"
                />

                <button
                    @click="sendProposal(proposal.id)"
                    class="mt-2 px-4 py-2 bg-black text-white rounded disabled:opacity-50"
                    :disabled="sendForm.processing"
                >
                    Enviar proposta ao cliente
                </button>
            </div>
        </li>

        <li v-if="!deal?.proposals?.length" class="text-gray-500">
            Ainda não existem propostas associadas a este negócio.
        </li>
    </ul>
</section>

<!-- PRODUTOS -->
<section class="mb-8 border rounded p-4">
    <div class="flex justify-between items-center mb-3">
        <h2 class="font-semibold">Produtos</h2>

        <button
            @click="showProductModal = true"
            class="px-3 py-1 bg-black text-white text-sm rounded"
        >
            + Adicionar Produto
        </button>
    </div>

    <ul v-if="deal?.products?.length" class="space-y-2">
        <li v-for="product in deal.products" :key="product.id" class="text-sm">
            {{ product.name }}
            · {{ product.pivot.quantity }} x {{ product.pivot.unit_price }}€
            = {{ product.pivot.total }}€
        </li>
    </ul>

    <p v-else class="text-sm text-gray-500">
        Nenhum produto associado.
    </p>
</section>

<!-- ATIVIDADES -->
<section class="mb-8 border rounded p-4 bg-gray-50">
    <h2 class="font-semibold mb-4">Nova atividade</h2>

    <div class="flex flex-col gap-3">
        <select v-model="activityForm.type" class="border p-2 rounded text-sm">
            <option value="note">Nota</option>
            <option value="call">Chamada</option>
            <option value="meeting">Reunião</option>
            <option value="task">Tarefa</option>
        </select>

        <textarea
            v-model="activityForm.description"
            rows="3"
            class="border p-2 rounded text-sm"
            placeholder="Descrição"
        />

        <input
            type="datetime-local"
            v-model="activityForm.due_at"
            class="border p-2 rounded text-sm"
        />

        <button
            @click="createActivity"
            class="self-start px-4 py-2 bg-black text-white rounded text-sm disabled:opacity-50"
            :disabled="activityForm.processing || !activityForm.description"
        >
            Guardar atividade
        </button>
    </div>
</section>

<!-- FOLLOW-UP -->
<section class="mb-8">
    <h2 class="font-semibold mb-3">Follow-up rápido</h2>

    <div class="flex flex-col gap-3">
        <select
            class="border rounded p-2 text-sm"
            @change="e => followUpForm.body = e.target.value"
        >
            <option value="">Selecionar mensagem pré-definida</option>
            <option
                v-for="(text, i) in followUpTemplates"
                :key="i"
                :value="text"
            >
                {{ text }}
            </option>
        </select>

        <div>
            <label class="block text-sm font-medium mb-1">
                Intervalo de follow-up
            </label>

            <select
                v-model="followUpForm.interval_days"
                class="border rounded p-2 text-sm w-full"
            >
                <option :value="2">A cada 2 dias</option>
                <option :value="5">A cada 5 dias</option>
                <option :value="7">A cada 7 dias</option>
            </select>
        </div>

        <textarea
            v-model="followUpForm.body"
            rows="3"
            class="border rounded p-2 text-sm"
            placeholder="Mensagem de follow-up"
        />

        <button
            @click="sendFollowUp"
            class="self-start px-4 py-2 bg-black text-white rounded disabled:opacity-50"
            :disabled="followUpForm.processing || !followUpForm.body"
        >
            Enviar follow-up
        </button>
    </div>
</section>

<!-- CRONOLOGIA -->
<section class="mt-10">
    <h2 class="font-semibold mb-4">Cronologia</h2>

    <!-- FILTROS -->
    <div class="flex flex-wrap gap-2 mb-4 text-xs">
        <button @click="timelineFilter='all'" :class="timelineFilter==='all'?activeFilterClass:filterClass">Todos</button>
        <button @click="timelineFilter='note'" :class="timelineFilter==='note'?activeFilterClass:filterClass">Notas</button>
        <button @click="timelineFilter='call'" :class="timelineFilter==='call'?activeFilterClass:filterClass">Chamadas</button>
        <button @click="timelineFilter='meeting'" :class="timelineFilter==='meeting'?activeFilterClass:filterClass">Reuniões</button>
        <button @click="timelineFilter='task'" :class="timelineFilter==='task'?activeFilterClass:filterClass">Tarefas</button>
        <button @click="timelineFilter='emails'" :class="timelineFilter==='emails'?activeFilterClass:filterClass">Emails</button>
        <button @click="timelineFilter='stage_changed'" :class="timelineFilter==='stage_changed'?activeFilterClass:filterClass">Estados</button>
    </div>

    <ul v-if="filteredTimeline().length" class="space-y-6">
        <li
            v-for="(item, index) in filteredTimeline()"
            :key="item.id ?? item.type + index"
            class="flex gap-4 items-start border-l-2 border-gray-200 pl-4 pb-4 cursor-pointer hover:bg-gray-50 rounded transition"
            @click="openTimelineModal(item)"
        >
            <div class="text-lg mt-0.5">
                <span v-if="item.type === 'deal_created'">📌</span>
                <span v-else-if="item.type === 'proposal_uploaded'">📄</span>
                <span v-else-if="item.type === 'proposal_sent'">✉️</span>
                <span v-else-if="item.type === 'follow_up'">🔁</span>
                <span v-else-if="item.type === 'stage_changed'">🔄</span>
                <span v-else-if="item.type === 'note'">📝</span>
                <span v-else-if="item.type === 'call'">📞</span>
                <span v-else-if="item.type === 'meeting'">📅</span>
                <span v-else-if="item.type === 'task'">✅</span>
                <span v-else-if="item.type === 'system_inactive'">⚠️</span>
                <span v-else>•</span>
            </div>

            <div>
                <p class="text-sm font-medium">{{ item.label }}</p>
                <p class="text-xs text-gray-500">
                    {{ item.user?.name ?? 'Sistema' }}
                    · {{ formatDate(item.date) }}
                </p>
            </div>
        </li>
    </ul>

    <p v-if="!filteredTimeline().length" class="text-sm text-gray-500">
        Nenhum evento encontrado para este filtro.
    </p>
</section>

<!-- MODAL TIMELINE -->
<BaseModal :show="showTimelineModal" @close="closeTimelineModal">
    <div v-if="selectedTimelineItem" class="space-y-4">
        <h3 class="text-lg font-semibold mb-4">
            {{ selectedTimelineItem.label }}
        </h3>

        <p class="text-sm text-gray-500 mb-4">
            {{ selectedTimelineItem.user?.name ?? 'Sistema' }}
            · {{ formatDate(selectedTimelineItem.date) }}
        </p>

        <div class="space-y-3 text-sm">
            <pre v-if="selectedTimelineItem.meta" class="bg-gray-50 p-3 rounded whitespace-pre-wrap">
{{ JSON.stringify(selectedTimelineItem.meta, null, 2) }}
            </pre>
        </div>

        <div class="mt-6 text-right">
            <button
                @click="closeTimelineModal"
                class="px-4 py-2 bg-black text-white rounded"
            >
                Fechar
            </button>
        </div>
    </div>
</BaseModal>

<!-- MODAL PRODUTOS -->
<BaseModal :show="showProductModal" @close="showProductModal=false">
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">Adicionar Produto</h3>

        <select
            v-model="productForm.product_id"
            class="w-full border p-2 rounded"
            @change="updateProductPrice(productForm.product_id)"
        >
            <option disabled value="">Selecionar produto</option>
            <option v-for="p in productsList" :key="p.id" :value="p.id">
                {{ p.name }} ({{ p.price }}€)
            </option>
        </select>

        <input
            type="number"
            min="1"
            v-model="productForm.quantity"
            class="w-full border p-2 rounded"
            placeholder="Quantidade"
        />

        <input
            type="number"
            step="0.01"
            v-model="productForm.unit_price"
            class="w-full border p-2 rounded"
            placeholder="Preço unitário"
        />

        <div class="text-sm text-gray-600">
            Total: <strong>{{ calculatedTotal() }}€</strong>
        </div>

        <div class="flex justify-end gap-2">
            <button
                @click="showProductModal=false"
                class="px-4 py-2 border rounded"
            >
                Cancelar
            </button>

            <button
                @click="attachProduct"
                class="px-4 py-2 bg-black text-white rounded disabled:opacity-50"
                :disabled="productForm.processing"
            >
                Guardar
            </button>
        </div>
    </div>
</BaseModal>

</CrmLayout>
</template>
