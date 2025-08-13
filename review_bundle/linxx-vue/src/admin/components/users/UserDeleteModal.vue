<template>
  <div v-if="modelValue" class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30" @click="$emit('update:modelValue', false)"></div>
    <div class="absolute left-1/2 top-1/2 w-full max-w-sm -translate-x-1/2 -translate-y-1/2 rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-white/10 dark:bg-[#0b0f1a]">
      <h3 class="text-base font-semibold mb-2">Delete user</h3>
      <p class="text-sm text-zinc-600 dark:text-zinc-300">Are you sure you want to delete <span class="font-medium">{{ user?.name }}</span>?</p>
      <div class="mt-5 flex items-center justify-end gap-2">
        <button @click="$emit('update:modelValue', false)" class="rounded-xl border px-3 py-2 text-sm dark:border-white/10">Cancel</button>
        <button @click="$emit('confirm')" :disabled="deleting" class="rounded-xl bg-rose-600 px-3 py-2 text-sm text-white hover:bg-rose-700 disabled:opacity-60">Delete</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Boolean, default: false },
  user: { type: Object, default: null },
  deleting: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue', 'confirm'])
</script>
