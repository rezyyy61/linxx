<template>
  <div class="flex items-center gap-2">
    <button @click="$emit('change:page', Math.max(1, page-1))" :disabled="busy || page<=1" class="rounded-xl border px-3 py-1.5 disabled:opacity-50 dark:border-white/10">Prev</button>
    <span class="px-2">Page {{ page }} / {{ lastPage }}</span>
    <button @click="$emit('change:page', Math.min(lastPage, page+1))" :disabled="busy || page>=lastPage" class="rounded-xl border px-3 py-1.5 disabled:opacity-50 dark:border-white/10">Next</button>
    <select :value="perPage" @change="$emit('change:perPage', Number($event.target.value))" class="rounded-xl border px-2 py-1.5 dark:border-white/10">
      <option :value="10">10</option>
      <option :value="20">20</option>
      <option :value="30">30</option>
    </select>
  </div>
</template>

<script setup>
const props = defineProps({
  page: { type: Number, required: true },
  perPage: { type: Number, required: true },
  total: { type: Number, required: true },
  lastPage: { type: Number, required: true },
  busy: { type: Boolean, default: false }
})
</script>
