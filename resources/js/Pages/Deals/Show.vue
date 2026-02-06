<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'


import { onMounted } from 'vue'
import axios from 'axios'

const followUpTemplates = ref([])
const followUpForm = useForm({
    body: '',
})

onMounted(async () => {
    const { data } = await axios.get('/follow-ups/templates')
    followUpTemplates.value = data
})

function sendFollowUp() {
    followUpForm.post(`/deals/${props.deal.id}/follow-ups`, {
        preserveScroll: true,
        onSuccess: () => {
            followUpForm.body = ''
        },
    })
}



const props = defineProps({
    deal: Object,
timeline: Array,
})

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const user = usePage().props.auth.user

/**
 * Upload de proposta
 */
const uploadForm = useForm({
    proposal: null,
})

function uploadProposal() {
    uploadForm.post(`/deals/${props.deal.id}/proposals`, {
        forceFormData: true,
    })
}

/**
 * Envio por email
 */
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
    })
}
</script>

<template>
    <CrmLayout>
        <!-- Cabeçalho -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold">{{ deal.title }}</h1>

            <p class="text-sm text-gray-600 mt-1">
                Valor: {{ deal.value ?? '—' }} €
                · Estado: {{ deal.stage }}
            </p>

            <p class="text-sm mt-1">
                <span v-if="deal.entity">Entidade: {{ deal.entity.name }}</span>
                <span v-if="deal.person"> · Pessoa: {{ deal.person.name }}</span>
            </p>
        </div>

        <!-- Upload de proposta -->
        <section class="mb-8">
            <h2 class="font-semibold mb-2">Adicionar proposta</h2>

            <form @submit.prevent="uploadProposal" class="flex items-center gap-4">
                <input
                    type="file"
                    @change="e => uploadForm.proposal = e.target.files[0]"
                />

                <button
                    class="px-4 py-2 bg-black text-white rounded"
                    :disabled="uploadForm.processing"
                >
                    Upload
                </button>
            </form>
        </section>

        <!-- Lista de propostas -->
        <section>
            <h2 class="font-semibold mb-3">Propostas</h2>

            <ul class="space-y-4">
                <li
                    v-for="proposal in deal.proposals"
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
                            Enviado em {{ proposal.sent_at }}
                        </span>
                    </div>

                    <!-- Enviar proposta -->
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
                            class="mt-2 px-4 py-2 bg-black text-white rounded"
                            :disabled="sendForm.processing"
                        >
                            Enviar proposta ao cliente
                        </button>
                    </div>
                </li>

                <li v-if="!deal.proposals.length" class="text-gray-500">
                    Ainda não existem propostas associadas a este negócio.
                </li>
            </ul>
        </section>


<!-- FOLLOW-UP MANUAL -->
<section class="mt-10">
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

        <textarea
            v-model="followUpForm.body"
            rows="3"
            class="border rounded p-2 text-sm"
            placeholder="Mensagem de follow-up"
        />

        <button
            @click="sendFollowUp"
            class="self-start px-4 py-2 bg-black text-white rounded"
            :disabled="followUpForm.processing || !followUpForm.body"
        >
            Enviar follow-up
        </button>
    </div>
</section>



         <!-- CRONOLOGIA
        <section class="mt-10">
            <h2 class="font-semibold mb-4">Cronologia</h2>

            <ul class="space-y-4">

                <li class="flex gap-3">
                    <span class="text-gray-400">✉️</span>
                    <div>
                        <p class="text-sm">
                            <strong>Proposta enviada ao cliente</strong>
                        </p>
                        <p class="text-xs text-gray-500">
                            Exemplo de evento (placeholder)
                        </p>
                    </div>
                </li>
            </ul>
        </section>
-->

      <!-- CRONOLOGIA -->
<section class="mt-10">
    <h2 class="font-semibold mb-4">Cronologia</h2>

    <ul v-if="timeline.length" class="space-y-4">
        <li
            v-for="item in timeline"
            :key="item.type + item.date"
            class="flex gap-3 items-start"
        >
            <!-- Ícone -->
            <div class="mt-0.5">
                <span v-if="item.type === 'deal_created'">📌</span>
                <span v-else-if="item.type === 'proposal_uploaded'">📄</span>
                <span v-else-if="item.type === 'proposal_sent'">✉️</span>
                <span v-else-if="item.type === 'follow_up'">🔁</span>
                <span v-else-if="item.type === 'stage_changed'">🔄</span>
                <span v-else>•</span>
            </div>

            <!-- Conteúdo -->
            <div class="flex-1">
                <p class="text-sm">
                    <strong>{{ item.label }}</strong>

                    <span v-if="item.meta?.name">
                        – {{ item.meta.name }}
                    </span>

                    <span
                        v-if="item.meta?.from && item.meta?.to"
                        class="text-gray-500"
                    >
                        ({{ item.meta.from }} → {{ item.meta.to }})
                    </span>
                </p>

                <p class="text-xs text-gray-500">
                    {{ item.user?.name ?? 'Sistema' }}
                    · {{ item.date ? new Date(item.date).toLocaleString() : '' }}

                </p>

                <p
                    v-if="item.meta?.body"
                    class="text-xs text-gray-600 mt-1 italic"
                >
                    "{{ item.meta.body }}"
                </p>
            </div>
        </li>
    </ul>

    <p v-else class="text-sm text-gray-500">
        Ainda não existem eventos na cronologia.
    </p>
</section>


    </CrmLayout>
</template>
