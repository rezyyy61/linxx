<template>
  <Transition name="fade">
    <div v-if="open">
      <div class="fixed inset-0 z-[9998]" @click="close"></div>

      <Teleport v-if="teleportToBody" to="body">
        <div class="fixed z-[9999]" :style="fixedStyle" @click.stop>
          <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 w-[280px] p-4 relative">
            <div class="absolute -top-1 left-1/2 -translate-x-1/2 h-2 w-2 rotate-45 bg-white dark:bg-neutral-900 border border-zinc-200 dark:border-zinc-800"></div>
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Share</h3>
              <button class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="close">
                <Icon icon="lucide:x" width="18" height="18" class="text-zinc-500" />
              </button>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <button
                  v-for="item in tiles" :key="item.key"
                  class="group flex flex-col items-center gap-2 p-3 rounded-xl border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition"
                  @click="select(item.key)"
              >
                <Icon :icon="item.icon" width="28" height="28" class="text-zinc-700 dark:text-zinc-200" />
                <span class="text-[11px] text-zinc-600 dark:text-zinc-300 text-center">{{ item.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </Teleport>

      <div v-else class="absolute z-50" :style="absoluteStyle" @click.stop>
        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 w-[280px] p-4 relative">
          <div class="absolute -top-1 left-1/2 -translate-x-1/2 h-2 w-2 rotate-45 bg-white dark:bg-neutral-900 border border-zinc-200 dark:border-zinc-800"></div>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Share</h3>
            <button class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="close">
              <Icon icon="lucide:x" width="18" height="18" class="text-zinc-500" />
            </button>
          </div>
          <div class="grid grid-cols-3 gap-3">
            <button
                v-for="item in tiles" :key="item.key"
                class="group flex flex-col items-center gap-2 p-3 rounded-xl border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition"
                @click="select(item.key)"
            >
              <Icon :icon="item.icon" width="28" height="28" class="text-zinc-700 dark:text-zinc-200" />
              <span class="text-[11px] text-zinc-600 dark:text-zinc-300 text-center">{{ item.label }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  position: { type: Object, default: () => ({ top: 0, left: 0 }) },
  teleportToBody: { type: Boolean, default: false },
  offset: { type: Number, default: 8 },
  zIndex: { type: Number, default: 9999 }
})
const emit = defineEmits(['update:modelValue', 'select'])

const open = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v)
})

const tiles = [
  { key: 'in-app', label: 'Inside app', icon: 'lucide:repeat-2' },
  { key: 'whatsapp', label: 'WhatsApp', icon: 'logos:whatsapp-icon' },
  { key: 'telegram', label: 'Telegram', icon: 'logos:telegram' },
  { key: 'x', label: 'X', icon: 'logos:twitter' },
  { key: 'facebook', label: 'Facebook', icon: 'logos:facebook' },
  { key: 'copy', label: 'Copy link', icon: 'lucide:clipboard-copy' },
]

const placement = computed(() => {
  const vw = window.innerWidth
  const vh = window.innerHeight
  const margin = 12
  const panelEstH = 240
  const belowTop = props.position.top + props.offset
  const aboveTop = props.position.top - props.offset
  const spaceBelow = belowTop + panelEstH <= vh - margin
  const top = spaceBelow ? belowTop : aboveTop
  const left = Math.min(Math.max(props.position.left, margin), vw - margin)
  const transform = spaceBelow ? 'translate(-50%, 0)' : 'translate(-50%, -100%)'
  return { top, left, transform }
})

const fixedStyle = computed(() => ({
  position: 'fixed',
  top: `${placement.value.top}px`,
  left: `${placement.value.left}px`,
  transform: placement.value.transform,
  zIndex: props.zIndex
}))

const absoluteStyle = computed(() => ({
  position: 'absolute',
  top: `${placement.value.top}px`,
  left: `${placement.value.left}px`,
  transform: placement.value.transform
}))

function close() { emit('update:modelValue', false) }
function select(key) { emit('select', { key }); close() }
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
