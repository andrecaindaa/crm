<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen flex bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r">
            <div class="p-4 font-bold text-lg border-b">
                CRM
            </div>

            <nav class="p-4 space-y-2">
                <Link href="/entities" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Entidades
                </Link>

                <Link href="/people" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Pessoas
                </Link>

                <Link href="/deals" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Negócios
                </Link>

                <Link href="/calendar" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Calendário
                </Link>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-6">
            <slot />
        </main>
    </div>
</template>
