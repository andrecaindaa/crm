<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'

defineProps({
    stats: Array
})


const showProductModal = ref(false)

const productForm = useForm({
    product_id: null,
    quantity: 1,
    unit_price: 0,
})

watch(() => productForm.product_id, (id) => {
    const selected = props.productsList.find(p => p.id === id)
    if (selected) {
        productForm.unit_price = selected.price
    }
})

const calculatedTotal = computed(() => {
    return (productForm.quantity * productForm.unit_price).toFixed(2)
})

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

</script>

<template>
<CrmLayout>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Estatísticas de Produtos</h1>
    </div>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-2">Produto</th>
                <th class="p-2">Qtd</th>
                <th class="p-2">Receita</th>
                <th class="p-2">Negócios</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in stats" :key="item.id" class="border-t">
                <td class="p-2">{{ item.name }}</td>
                <td class="p-2">{{ item.total_quantity }}</td>
                <td class="p-2">{{ Number(item.total_revenue).toFixed(2) }}€</td>
                <td class="p-2">{{ item.total_deals }}</td>
            </tr>
        </tbody>
    </table>

</CrmLayout>
</template>
