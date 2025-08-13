<template>
  <div class="space-y-6 w-[92%] sm:w-[90%] mx-auto py-8">
    <EventHeader
        v-model:searchQuery="searchQuery"
        v-model:statusFilter="statusFilter"
        @create="openCreate"
    />
    <EventList
        v-if="!showForm"
        :search-query="searchQuery"
        :status-filter="statusFilter"
        :cover-mode="'contain'"
        :show-jalali="true"
        @edit="openEdit"
        @create="openCreate"
    />
    <div v-else class="rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#0b0f1a] shadow-sm">
      <div class="top-20 z-10 border-b border-zinc-200 dark:border-white/10 bg-white/80 dark:bg-[#0b0f1a]/80 backdrop-blur-xl px-4 sm:px-6 py-3 rounded-t-2xl">
        <div class="flex items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <button
                @click="closeForm"
                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 border border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 text-zinc-700 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-rose-500/60 dark:focus:ring-fuchsia-500/50 transition"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
              <span class="text-sm">{{ $t('dashboard.event.actions.back') }}</span>
            </button>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
              {{ selectedEvent?.id ? $t('dashboard.event.editTitle') : $t('dashboard.event.createTitle') }}
            </h3>
          </div>
        </div>
      </div>
      <div class="p-4 sm:p-6">
        <EventForm :model-value="selectedEvent" @submit="saveEvent" @cancel="closeForm" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useEventStore } from '@/stores/events/events.store'
import EventForm from '@/pages/admin/sections/Event/EventForm.vue'
import EventList from '@/pages/admin/sections/Event/EventList.vue'
import EventHeader from '@/pages/admin/sections/Event/EventHeader.vue'

const store = useEventStore()

const showForm = ref(false)
const selectedEvent = ref(null)
const searchQuery = ref('')
const statusFilter = ref('all')

function openCreate() {
  selectedEvent.value = null
  showForm.value = true
}

function openEdit(ev) {
  showForm.value = true
  store.fetchOne(ev.id, { force: true })
      .then(full => { selectedEvent.value = { ...full } })
      .catch(() => { selectedEvent.value = { ...ev } })
}

function closeForm() {
  showForm.value = false
  selectedEvent.value = null
}

async function saveEvent(data) {
  const { id, files, ...payload } = data || {}
  if (id) {
    const updated = await store.update(id, payload, files || {})
    selectedEvent.value = { ...updated }
  } else {
    await store.create(payload, files || {})
  }
  closeForm()
}
</script>
