<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

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

        <section class="mt-10">
    <h2 class="font-semibold mb-4">Cronologia</h2>

    <ul class="space-y-4">
        <li
            v-for="(item, index) in timeline"
            :key="`timeline-${item.type}-${item.id || index}`"
            class="flex gap-3"
        >
            <!-- Ícone -->
            <span class="text-gray-400 mt-0.5">
                <span v-if="item.type === 'deal_created'">📌</span>
                <span v-else-if="item.type === 'proposal_uploaded'">📄</span>
                <span v-else-if="item.type === 'proposal_sent'">✉️</span>
                <span v-else-if="item.type === 'follow_up'">🔁</span>
                <span v-else-if="item.type === 'stage_changed'">🔄</span>
                <span v-else>•</span>
            </span>

            <div class="flex-1">
                <p class="text-sm">
                    <strong>{{ item.label }}</strong>
                    <span v-if="item.meta?.name" class="text-gray-600">
                        – {{ item.meta.name }}
                    </span>
                </p>

                <p class="text-xs text-gray-500">
                    {{ item.user?.name ?? 'Sistema' }}
                    · {{ formatDate(item.date) }}
                </p>

                <!-- Corpo do email/observação -->
                <p
                    v-if="item.meta?.body"
                    class="text-xs text-gray-600 mt-1 whitespace-pre-line"
                >
                    "{{ item.meta.body }}"
                </p>

                <!-- Mudança de estado -->
                <p
                    v-if="item.meta?.from"
                    class="text-xs text-gray-600 mt-1"
                >
                    Estado: {{ item.meta.from }} → {{ item.meta.to }}
                </p>
            </div>
        </li>

        <li v-if="!timeline?.length" class="text-gray-500 text-sm italic">
            Ainda não existem eventos registados.
        </li>
    </ul>
</section>

    </CrmLayout>
</template>
