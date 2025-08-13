<template>
  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div class="flex w-full md:max-w-xl items-center gap-2">
      <div class="relative flex-1">
        <input
            :value="qLocal"
            @input="onInput"
            type="text"
            class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-rose-500/30 dark:border-white/10 dark:bg-[#0b0f1a] dark:text-white"
            placeholder="Search name, email, slug…"
        />
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400">⌘K</span>
      </div>
      <select
          :value="entityType"
          @change="onType($event.target.value)"
          class="rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0b0f1a] dark:text-white"
      >
        <option value="">All types</option>
        <option value="individual">Individual</option>
        <option value="party">Party</option>
        <option value="collective">Collective</option>
        <option value="media">Media</option>
      </select>
    </div>
    <div class="flex items-center gap-2">
      <button @click="emit('refresh')" :disabled="loading" class="rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 disabled:opacity-60 dark:border-white/10 dark:hover:bg-white/5">
        Refresh
      </button>
      <button @click="reset" class="rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">
        Reset
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  q: { type: String, default: '' },
  entityType: { type: String, default: '' },
  loading: { type: Boolean, default: false }
})
const emit = defineEmits(['change', 'refresh'])

const qLocal = ref(props.q)
let t = null

function onInput(e) {
  qLocal.value = e.target.value
  clearTimeout(t)
  t = setTimeout(() => emit('change', { q: qLocal.value }), 350)
}
function onType(v) {
  emit('change', { entity_type: v })
}
function reset() {
  qLocal.value = ''
  emit('change', { q: '', entity_type: '' })
}
</script>
