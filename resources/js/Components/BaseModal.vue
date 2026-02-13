<script setup>
import { onMounted, onUnmounted } from 'vue'

const props = defineProps({
    show: Boolean,
})

const emit = defineEmits(['close'])

function handleEsc(e) {
    if (e.key === 'Escape') emit('close')
}

onMounted(() => window.addEventListener('keydown', handleEsc))
onUnmounted(() => window.removeEventListener('keydown', handleEsc))
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">

        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-black/40 backdrop-blur-sm"
            @click="$emit('close')"
        />

        <!-- Modal -->
        <div class="relative bg-white w-full max-w-lg mx-4 rounded-xl shadow-xl p-6 z-10">
            <slot />
        </div>
    </div>
</template>

