<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
    entities: Array
})

const form = useForm({
    entity_id: '',
    name: '',
    email: '',
    phone: '',
    position: '',
    notes: '',
})
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-4">Nova Pessoa</h1>

        <form @submit.prevent="form.post('/people')" class="space-y-4 max-w-xl">
            <select v-model="form.entity_id" class="w-full p-2 border rounded">
                <option value="">Sem entidade</option>
                <option v-for="entity in entities" :value="entity.id">
                    {{ entity.name }}
                </option>
            </select>

            <input v-model="form.name" placeholder="Nome" class="w-full p-2 border rounded" />
            <input v-model="form.email" placeholder="Email" class="w-full p-2 border rounded" />
            <input v-model="form.phone" placeholder="Telefone" class="w-full p-2 border rounded" />
            <input v-model="form.position" placeholder="Cargo" class="w-full p-2 border rounded" />

            <textarea v-model="form.notes" placeholder="Notas" class="w-full p-2 border rounded" />

            <button class="px-4 py-2 bg-black text-white rounded">
                Guardar
            </button>
        </form>
    </CrmLayout>
</template>
