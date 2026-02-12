<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { ref, onMounted } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    deal: Object,
    timeline: Array,
    activeFollowUp: Object,
})

const user = usePage().props.auth.user

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
    const { data } = await axios.get('/follow-ups/templates')
    followUpTemplates.value = data
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
| ATIVIDADES RÁPIDAS
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

function completeActivity(id) {
    router.patch(`/activities/${id}/complete`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['timeline'] })
        },
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


        <!-- ATIVIDADES RÁPIDAS -->
<section class="mt-10 border rounded p-4 bg-gray-50">
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
            class="self-start px-4 py-2 bg-black text-white rounded text-sm"
            :disabled="activityForm.processing || !activityForm.description"
        >
            Guardar atividade
        </button>
    </div>
</section>



<!-- FOLLOW-UP MANUAL -->
<section class="mt-10">
    <h2 class="font-semibold mb-3">Follow-up rápido</h2>

    <div class="flex flex-col gap-3">

        <!-- Templates -->
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

        <!--  INTERVALO DE FOLLOW-UP -->
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

        <!-- Mensagem -->
        <textarea
            v-model="followUpForm.body"
            rows="3"
            class="border rounded p-2 text-sm"
            placeholder="Mensagem de follow-up"
        />

        <!-- Enviar -->
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

    <ul v-if="timeline.length" class="space-y-6">

        <li
            v-for="item in timeline"
            :key="item.type + item.date"
            class="flex gap-4 items-start border-l-2 pl-4 pb-4"
        >
            <!-- Ícone -->
            <div class="text-lg mt-0.5">
                <span v-if="item.type === 'deal_created'">📌</span>
                <span v-else-if="item.type === 'proposal_uploaded'">📄</span>
                <span v-else-if="item.type === 'proposal_sent'">✉️</span>
                <span v-else-if="item.type === 'follow_up'">🔁</span>
                <span v-else-if="item.type === 'stage_changed'">🔄</span>
                <span v-else>•</span>
            </div>

            <!-- Conteúdo -->
            <div class="flex-1">
                <p class="text-sm font-medium">
                    {{ item.label }}

                    <span v-if="item.meta?.name" class="text-gray-600">
                        – {{ item.meta.name }}
                    </span>

                    <span
                        v-if="item.meta?.from && item.meta?.to"
                        class="text-gray-500"
                    >
                        ({{ item.meta.from }} → {{ item.meta.to }})
                    </span>
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    {{ item.user?.name ?? 'Sistema' }}
                    · {{ new Date(item.date).toLocaleString('pt-PT') }}
                </p>

                <p
    v-if="item.meta?.body"
    class="text-xs text-gray-600 mt-2 italic bg-gray-50 p-2 rounded"
>
    "{{ item.meta.body }}"
</p>

<p
    v-if="item.meta?.description"
    class="text-xs text-gray-600 mt-2"
>
    {{ item.meta.description }}
</p>

<p
    v-if="item.meta?.due_at"
    class="text-xs text-gray-500 mt-1"
>
    Prazo: {{ new Date(item.meta.due_at).toLocaleString('pt-PT') }}
</p>

<button
    v-if="item.type === 'task' && !item.meta?.completed_at"
    @click="completeActivity(item.id)"
    class="mt-2 text-xs bg-green-600 text-white px-2 py-1 rounded"
>
    Marcar como concluída
</button>

<p
    v-if="item.meta?.completed_at"
    class="text-xs text-green-600 mt-1"
>
    Concluída em {{ new Date(item.meta.completed_at).toLocaleString('pt-PT') }}
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
