<template>
  <button
      class="relative w-11 h-11 rounded-xl flex items-center justify-center
           text-gray-700 bg-gray-100 hover:bg-gray-200
           dark:text-gray-200 dark:bg-[#1a1f2b] dark:hover:bg-[#222937]
           transition"
      :class="highlight ? 'ring-1 ring-red-500/40' : ''"
      @click="$emit('click')"
      :title="label"
      aria-label="label"
  >
    <Icon :icon="icon" class="w-5 h-5" />
    <span v-if="Number(badge) > 0"
          class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full
                 bg-red-600 text-white text-[10px] font-bold flex items-center justify-center">
      {{ badge }}
    </span>
    <span class="sr-only">{{ label }}</span>
  </button>
</template>

<script>
import { Icon } from '@iconify/vue'

export default {
  name: 'EdgeAction',
  components: { Icon },
  props: {
    icon: { type: String, required: true },
    label: { type: String, required: true },
    badge: { type: [Number, String], default: 0 },
    highlight: { type: Boolean, default: false },
  },
  emits: ['click'],
}
</script>
