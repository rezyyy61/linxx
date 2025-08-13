<template>
  <div class="relative inline-block" ref="btnWrap">
    <button
        class="rounded-lg border px-3 py-1.5 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5 inline-flex items-center gap-2"
        @click="toggle"
        :disabled="loading"
        ref="btn"
    >
      <span :class="dotClass(current)"></span>
      <span>{{ badgeLabel }}</span>
      <Icon icon="mdi:chevron-down" class="w-4 h-4 opacity-70" />
    </button>

    <teleport to="body">
      <div
          v-if="open"
          ref="menu"
          class="z-[9999] fixed w-48 rounded-xl border border-zinc-200 bg-white p-1 shadow-lg dark:border-white/10 dark:bg-[#0b0f1a]"
          :style="menuStyle"
      >
        <button
            v-for="opt in visibleOptions"
            :key="opt.key"
            class="w-full text-left rounded-lg px-2.5 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5 inline-flex items-center gap-2 disabled:opacity-50"
            @click="apply(opt)"
            :disabled="loading || opt.disabled"
            :title="t(`dashboard.event.statusHelp.${opt.key}`)"
        >
          <span :class="dotClass(opt.to)"></span>
          <span>{{ t(`dashboard.event.statusActions.${opt.key}`) }}</span>
        </button>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import { useEventStore } from '@/stores/events/events.store'

const props = defineProps({ event: { type: Object, required: true }, align: { type: String, default: 'end' } })
const emit = defineEmits(['changed'])
const { t } = useI18n({ useScope: 'global' })
const store = useEventStore()

const open = ref(false)
const loading = ref(false)
const btn = ref(null)
const menu = ref(null)
const pos = ref({ top: 0, left: 0, right: null })

const current = computed(() => String(props.event?.status || 'draft').toLowerCase())
const badgeLabel = computed(() => t(`dashboard.event.statuses.${current.value}`))

const menuStyle = computed(() => {
  const base = { top: `${pos.value.top}px` }
  if (props.align === 'start') return { ...base, left: `${pos.value.left}px` }
  return { ...base, right: `${pos.value.right}px` }
})

function calcPos() {
  const r = btn.value?.getBoundingClientRect()
  if (!r) return
  const top = r.bottom + 8
  if (props.align === 'start') {
    pos.value = { top, left: r.left, right: null }
  } else {
    pos.value = { top, left: 0, right: window.innerWidth - r.right }
  }
}

function toggle() {
  open.value = !open.value
  if (open.value) nextTick(calcPos)
}
function close() { open.value = false }

function dotClass(s) {
  const base = 'inline-block h-2.5 w-2.5 rounded-full'
  if (s === 'published') return `${base} bg-emerald-500`
  if (s === 'cancelled') return `${base} bg-rose-500`
  if (s === 'archived') return `${base} bg-zinc-400`
  return `${base} bg-amber-500`
}

function isEnded() {
  try {
    const now = Date.now()
    const e = props.event?.ends_at ? new Date(props.event.ends_at).getTime() : null
    if (e == null) return false
    return e < now
  } catch { return false }
}

const options = computed(() => {
  if (current.value === 'draft') return [{ key: 'publish', to: 'published' }]
  if (current.value === 'published') {
    const ended = isEnded()
    const base = [{ key: 'moveToDraft', to: 'draft' }, { key: 'cancel', to: 'cancelled' }]
    return ended ? [...base, { key: 'archive', to: 'archived' }] : base
  }
  if (current.value === 'cancelled') return [{ key: 'restore', to: 'draft' }, { key: 'archive', to: 'archived' }]
  if (current.value === 'archived') return [{ key: 'unarchive', to: 'draft' }]
  return []
})

const visibleOptions = computed(() => options.value.map(o => ({ ...o, disabled: false })))

async function apply(opt) {
  if (!props.event?.id) return
  loading.value = true
  try {
    const updated = await store.updateStatus(props.event.id, opt.to)
    emit('changed', updated)
  } finally {
    loading.value = false
    close()
  }
}

function onDocClick(e) {
  if (open.value && !btn.value?.contains(e.target) && !menu.value?.contains(e.target)) close()
}
function onWin() { if (open.value) calcPos() }

onMounted(() => {
  document.addEventListener('click', onDocClick)
  window.addEventListener('scroll', onWin, true)
  window.addEventListener('resize', onWin)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', onDocClick)
  window.removeEventListener('scroll', onWin, true)
  window.removeEventListener('resize', onWin)
})
</script>
