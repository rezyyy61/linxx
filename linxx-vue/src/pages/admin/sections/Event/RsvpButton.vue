<!-- /src/pages/admin/sections/Event/RsvpButton.vue -->
<template>
  <div class="inline-flex items-center gap-2">
    <div class="rounded-2xl p-[1px] bg-gradient-to-r from-fuchsia-500 via-rose-500 to-orange-500">
      <div class="flex rounded-2xl overflow-hidden bg-white dark:bg-[#0e1526] border border-zinc-200 dark:border-white/10">
        <button
            :disabled="saving"
            @click="choose('going')"
            :class="btnClass('going')"
        >
          <span class="i">✅</span>
          <span>Going</span>
        </button>
        <button
            :disabled="saving"
            @click="choose('interested')"
            :class="btnClass('interested')"
        >
          <span class="i">⭐</span>
          <span>Interested</span>
        </button>
        <button
            :disabled="saving"
            @click="choose('not_going')"
            :class="btnClass('not_going')"
        >
          <span class="i">🚫</span>
          <span>Not going</span>
        </button>
      </div>
    </div>

    <button
        v-if="current"
        :disabled="saving"
        @click="cancel"
        class="rounded-xl px-3 py-2 text-sm border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#0b0f1a] text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
    >
      Cancel
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useRsvpStore } from '@/stores/events/rsvp.store'

const props = defineProps({ eventId: { type: [Number, String], required: true } })
const store = useRsvpStore()
const { myStatus, isSaving } = storeToRefs(store)
const current = computed(() => myStatus.value(props.eventId))
const saving = computed(() => isSaving.value(props.eventId))

function btnClass(key) {
  const active = current.value === key
  return [
    'px-3 sm:px-4 py-2 text-sm font-medium flex items-center gap-2 select-none transition',
    active
        ? 'bg-gradient-to-r from-fuchsia-600 to-rose-600 text-white shadow-inner'
        : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-white/5'
  ]
}
async function choose(status) {
  if (current.value === status) return
  await store.setStatus(Number(props.eventId), status)
}
async function cancel() {
  await store.cancel(Number(props.eventId))
}
</script>

<style scoped>
.i { font-size: 14px }
</style>
