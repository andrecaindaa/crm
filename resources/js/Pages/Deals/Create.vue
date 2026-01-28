<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
    entities: Array,
    people: Array,
})

const form = useForm({
    title: '',
    entity_id: '',
    person_id: '',
    value: '',
    stage: 'lead',
})
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-6">Novo Negócio</h1>

        <form
            @submit.prevent="form.post('/deals')"
            class="space-y-4 max-w-xl"
        >
            <input
                v-model="form.title"
                placeholder="Título"
                class="w-full p-2 border rounded"
            />

            <select v-model="form.entity_id" class="w-full p-2 border rounded">
                <option value="">Sem entidade</option>
                <option
                    v-for="entity in entities"
                    :key="entity.id"
                    :value="entity.id"
                >
                    {{ entity.name }}
                </option>
            </select>

            <select v-model="form.person_id" class="w-full p-2 border rounded">
                <option value="">Sem pessoa</option>
                <option
                    v-for="person in people"
                    :key="person.id"
                    :value="person.id"
                >
                    {{ person.name }}
                </option>
            </select>

            <input
                v-model="form.value"
                type="number"
                placeholder="Valor (€)"
                class="w-full p-2 border rounded"
            />

            <button class="px-4 py-2 bg-black text-white rounded">
                Criar
            </button>
        </form>
    </CrmLayout>
</template>
