<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    person: Object,
    entities: Array,
})

const form = useForm({
    entity_id: props.person.entity_id,
    name: props.person.name,
    email: props.person.email,
    phone: props.person.phone,
    position: props.person.position,
    notes: props.person.notes,
})
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-4">Editar Pessoa</h1>

        <form
            @submit.prevent="form.put(`/people/${props.person.id}`)"
            class="space-y-4 max-w-xl"
        >
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

            <input v-model="form.name" class="w-full p-2 border rounded" />
            <input v-model="form.email" class="w-full p-2 border rounded" />
            <input v-model="form.phone" class="w-full p-2 border rounded" />
            <input v-model="form.position" class="w-full p-2 border rounded" />

            <textarea v-model="form.notes" class="w-full p-2 border rounded" />

            <button class="px-4 py-2 bg-black text-white rounded">
                Atualizar
            </button>
        </form>
    </CrmLayout>
</template>
