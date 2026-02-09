<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import { useForm } from '@inertiajs/vue3'

// Recebe a entidade como prop
const props = defineProps({
    entity: {
        type: Object,
        required: true
    }
})

// Inicializa o form com os dados da entidade
const form = useForm({
    name: props.entity.name,
    vat: props.entity.vat,
    email: props.entity.email,
    phone: props.entity.phone,
    address: props.entity.address,
    status: props.entity.status,
})

// Função para submeter o formulário de atualização
const submit = () => {
    form.put(`/entities/${props.entity.id}`)
}
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-4">Editar Entidade</h1>

        <form @submit.prevent="submit" class="space-y-4 max-w-xl">
            <div>
                <label class="block text-sm font-medium mb-1">Nome</label>
                <input
                    v-model="form.name"
                    placeholder="Nome"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">VAT</label>
                <input
                    v-model="form.vat"
                    placeholder="VAT"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': form.errors.vat }"
                />
                <p v-if="form.errors.vat" class="text-red-500 text-sm mt-1">
                    {{ form.errors.vat }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    placeholder="Email"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': form.errors.email }"
                />
                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Telefone</label>
                <input
                    v-model="form.phone"
                    placeholder="Telefone"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': form.errors.phone }"
                />
                <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">
                    {{ form.errors.phone }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Morada</label>
                <textarea
                    v-model="form.address"
                    placeholder="Morada"
                    rows="3"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': form.errors.address }"
                />
                <p v-if="form.errors.address" class="text-red-500 text-sm mt-1">
                    {{ form.errors.address }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select
                    v-model="form.status"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': form.errors.status }"
                >
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                    <option value="pending">Pendente</option>
                </select>
                <p v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                    {{ form.errors.status }}
                </p>
            </div>

            <div class="flex space-x-4 pt-4">
                <button
                    type="submit"
                    class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800 transition"
                    :disabled="form.processing"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                >
                    <span v-if="form.processing">A guardar...</span>
                    <span v-else>Guardar Alterações</span>
                </button>

                <button
                    type="button"
                    @click="$inertia.get('/entities')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition"
                    :disabled="form.processing"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    @click="form.delete(`/entities/${entity.id}`)"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition ml-auto"
                    :disabled="form.processing"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    onclick="return confirm('Tem a certeza que deseja eliminar esta entidade?')"
                >
                    Eliminar
                </button>
            </div>
        </form>
    </CrmLayout>
</template>
