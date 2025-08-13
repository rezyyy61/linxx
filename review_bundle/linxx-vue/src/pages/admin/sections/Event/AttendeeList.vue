<!-- /src/pages/admin/sections/Event/AttendeeList.vue -->
<template>
  <div class="rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#0b0f1a]">
    <div class="px-4 sm:px-6 py-4 border-b border-zinc-200 dark:border-white/10 flex items-center justify-between">
      <h4 class="font-semibold text-zinc-900 dark:text-white">Attendees</h4>
      <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ items.length }}</span>
    </div>

    <div v-if="loading" class="p-4 sm:p-6 grid gap-3">
      <div v-for="n in 4" :key="n" class="h-10 rounded-xl bg-zinc-100 dark:bg-white/5 animate-pulse"></div>
    </div>

    <ul v-else class="divide-y divide-zinc-200 dark:divide-white/10">
      <li v-for="(a, i) in items" :key="i" class="p-4 sm:p-5 flex items-center gap-3">
        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-fuchsia-500 to-rose-500 text-white grid place-items-center overflow-hidden">
          <img v-if="a.user?.avatar_url" :src="a.user.avatar_url" alt="" class="h-full w-full object-cover" />
          <span v-else class="text-sm font-semibold">{{ initials(a.user?.name) }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ a.user?.name || 'User' }}</div>
          <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ a.user?.email }}</div>
        </div>
        <span :class="badgeClass(a.status)">{{ label(a.status) }}</span>
      </li>
      <li v-if="!items.length" class="p-6 text-sm text-zinc-500 dark:text-zinc-400">No attendees yet.</li>
    </ul>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useRsvpStore } from '@/stores/events/rsvp.store'

const props = defineProps({ eventId: { type: [Number, String], required: true }, autoload: { type: Boolean, default: true } })
const store = useRsvpStore()
const { attendees, isLoading } = storeToRefs(store)
const items = computed(() => attendees.value(props.eventId))
const loading = computed(() => isLoading.value(props.eventId))

onMounted(() => { if (props.autoload) store.fetchAttendees(Number(props.eventId)) })

function initials(name) {
  if (!name) return 'U'
  const parts = String(name).trim().split(' ').filter(Boolean)
  return (parts[0]?.[0] || '') + (parts[1]?.[0] || '')
}
function label(s) {
  if (s === 'going') return 'Going'
  if (s === 'interested') return 'Interested'
  if (s === 'not_going') return 'Not going'
  return s || 'RSVP'
}
function badgeClass(s) {
  if (s === 'going') return 'text-xs px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
  if (s === 'interested') return 'text-xs px-2 py-1 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'
  if (s === 'not_going') return 'text-xs px-2 py-1 rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'
  return 'text-xs px-2 py-1 rounded-lg bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200'
}
</script>
