<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    vat: '',
    email: '',
    phone: '',
    address: '',
    status: 'active',
})
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-4">Nova Entidade</h1>

        <form @submit.prevent="form.post('/entities')" class="space-y-4 max-w-xl">
            <input v-model="form.name" placeholder="Nome" class="w-full p-2 border rounded" />
            <input v-model="form.vat" placeholder="VAT" class="w-full p-2 border rounded" />
            <input v-model="form.email" placeholder="Email" class="w-full p-2 border rounded" />
            <input v-model="form.phone" placeholder="Telefone" class="w-full p-2 border rounded" />
            <input v-model="form.address" placeholder="Morada" class="w-full p-2 border rounded" />

            <button class="px-4 py-2 bg-black text-white rounded">
                Guardar
            </button>
        </form>
    </CrmLayout>
</template>
