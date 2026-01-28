<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

defineProps({
    events: Array,
})

const calendarOptions = {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    events: [],
}
</script>

<template>
    <CrmLayout>
        <h1 class="text-2xl font-bold mb-6">Calendário</h1>

        <FullCalendar
            :options="{
                ...calendarOptions,
                events: events
            }"
        />
    </CrmLayout>
</template>
