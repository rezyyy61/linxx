<template>
  <section class="sticky top-0 z-30 bg-white/85 dark:bg-[#0b0f1a]/85 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:supports-[backdrop-filter]:bg-[#0b0f1a]/60 border-b border-zinc-200 dark:border-white/10">
    <div class="mx-auto max-w-7xl px-4 py-3">
      <div class="flex flex-wrap items-center gap-3">
        <!-- Type -->
        <div class="relative">
          <button ref="btnType" class="fx-btn" @click="toggle('type')">
            <Icon icon="mdi:tune-variant" class="w-4 h-4 opacity-70" />
            <span>{{ t('home.event.filters.type.label') }}</span>
            <Icon icon="mdi:chevron-down" class="w-4 h-4 opacity-70" />
          </button>
          <teleport to="body">
            <div v-if="open.type" ref="menuType" class="fx-menu" :style="styleOf('type')">
              <button
                  v-for="opt in typeOptions"
                  :key="opt.value"
                  class="fx-item"
                  :class="{'!bg-zinc-100 dark:!bg-white/10': form.type === opt.value}"
                  @click="form.type = opt.value; closeAll()"
              >
                {{ opt.label }}
              </button>
            </div>
          </teleport>
        </div>

        <!-- Mode -->
        <div class="relative">
          <button ref="btnMode" class="fx-btn" @click="toggle('mode')">
            <Icon icon="mdi:earth" class="w-4 h-4 opacity-70" />
            <span>{{ t('home.event.filters.mode.label') }}</span>
            <Icon icon="mdi:chevron-down" class="w-4 h-4 opacity-70" />
          </button>
          <teleport to="body">
            <div v-if="open.mode" ref="menuMode" class="fx-menu" :style="styleOf('mode')">
              <button class="fx-item" :class="{'!bg-zinc-100 dark:!bg-white/10': form.mode === ''}" @click="form.mode=''; closeAll()">
                {{ t('home.event.filters.mode.all') }}
              </button>
              <button class="fx-item" :class="{'!bg-zinc-100 dark:!bg-white/10': form.mode === 'offline'}" @click="form.mode='offline'; closeAll()">
                {{ t('home.event.filters.mode.offline') }}
              </button>
              <button class="fx-item" :class="{'!bg-zinc-100 dark:!bg-white/10': form.mode === 'online'}" @click="form.mode='online'; closeAll()">
                {{ t('home.event.filters.mode.online') }}
              </button>
              <button class="fx-item" :class="{'!bg-zinc-100 dark:!bg-white/10': form.mode === 'hybrid'}" @click="form.mode='hybrid'; closeAll()">
                {{ t('home.event.filters.mode.hybrid') }}
              </button>
            </div>
          </teleport>
        </div>

        <!-- Date preset -->
        <div class="relative">
          <button ref="btnDate" class="fx-btn" @click="toggle('date')">
            <Icon icon="mdi:calendar-range" class="w-4 h-4 opacity-70" />
            <span>{{ t('home.event.filters.date.label') }}</span>
            <Icon icon="mdi:chevron-down" class="w-4 h-4 opacity-70" />
          </button>
          <teleport to="body">
            <div v-if="open.date" ref="menuDate" class="fx-menu" :style="styleOf('date')">
              <button
                  v-for="opt in dateOptions"
                  :key="opt.value"
                  class="fx-item"
                  :class="{'!bg-zinc-100 dark:!bg-white/10': form.datePreset === opt.value}"
                  @click="form.datePreset = opt.value; closeAll()"
              >
                {{ opt.label }}
              </button>
            </div>
          </teleport>
        </div>

        <!-- Search -->
        <div class="relative flex-1 min-w-[220px]">
          <Icon icon="mdi:magnify" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 opacity-60" />
          <input
              v-model.trim="form.q"
              :placeholder="t('home.event.filters.search_placeholder')"
              class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-zinc-300 dark:border-white/10 bg-white dark:bg-transparent focus:outline-none focus:ring-2 focus:ring-rose-500/30"
              type="search"
              @input="onSearchInput"
              @keyup.enter="applyFilters"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import { usePublicEventsStore } from '@/stores/events/public/public.store'

const { t } = useI18n({ useScope: 'global' })
const store = usePublicEventsStore()
const loading = computed(() => store.listLoading)

const typeKeys = ['rally_protest', 'town_hall', 'meeting_internal', 'training', 'webinar', 'fundraiser']
const typeOptions = computed(() => [
  { value: '', label: t('home.event.filters.type.all') },
  ...typeKeys.map(v => ({ value: v, label: t(`home.event.types.${v}`) }))
])

const dateOptions = [
  { value: 'any', label: t('home.event.filters.date.any') },
  { value: 'today', label: t('home.event.filters.date.today') },
  { value: 'tomorrow', label: t('home.event.filters.date.tomorrow') },
  { value: 'next_7', label: t('home.event.filters.date.next7') },
  { value: 'next_30', label: t('home.event.filters.date.next30') }
]

const form = reactive({
  q: store.filters.q || '',
  type: store.filters.type || '',
  mode: store.filters.mode || '',
  datePreset: 'any'
})

/* --- positioning for floating menus --- */
const open = ref({ type: false, mode: false, date: false })
const btnType = ref(null); const btnMode = ref(null); const btnDate = ref(null)
const menuType = ref(null); const menuMode = ref(null); const menuDate = ref(null)
const pos = ref({
  type: { top: 0, left: 0 },
  mode: { top: 0, left: 0 },
  date: { top: 0, left: 0 }
})

function toggle(key) {
  Object.keys(open.value).forEach(k => (open.value[k] = k === key ? !open.value[k] : false))
  if (open.value[key]) {
    nextTick(() => {
      const map = { type: btnType.value, mode: btnMode.value, date: btnDate.value }
      calcPos(map[key], key)
    })
  }
}
function closeAll() { Object.keys(open.value).forEach(k => (open.value[k] = false)) }
function calcPos(el, key) {
  if (!el) return
  const r = el.getBoundingClientRect()
  pos.value[key] = { top: r.bottom + 8, left: r.left }
}
function styleOf(key) {
  const p = pos.value[key] || { top: 0, left: 0 }
  return { top: `${p.top}px`, left: `${p.left}px` }
}
function onDocClick(e) {
  const menus = [menuType.value, menuMode.value, menuDate.value]
  const btns = [btnType.value, btnMode.value, btnDate.value]
  const inside = [...menus, ...btns].some(el => el && el.contains(e.target))
  if (!inside) closeAll()
}
function onWin() {
  Object.keys(open.value).forEach(k => {
    if (open.value[k]) {
      const map = { type: btnType.value, mode: btnMode.value, date: btnDate.value }
      calcPos(map[k], k)
    }
  })
}
onMounted(() => {
  document.addEventListener('click', onDocClick)
  window.addEventListener('resize', onWin)
  window.addEventListener('scroll', onWin, true)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', onDocClick)
  window.removeEventListener('resize', onWin)
  window.removeEventListener('scroll', onWin, true)
})

/* --- instant filtering --- */
let timer = null
function onSearchInput() {
  if (timer) clearTimeout(timer)
  timer = setTimeout(() => applyFilters(), 300)
}

// هر تغییری در Type/Mode/DatePreset به‌صورت خودکار اعمال می‌شود
watch(() => [form.type, form.mode, form.datePreset], () => applyFilters())

function mapDatePreset(preset) {
  const today = new Date()
  const ymd = (d) => {
    const y = d.getFullYear()
    const m = String(d.getMonth() + 1).padStart(2, '0')
    const da = String(d.getDate()).padStart(2, '0')
    return `${y}-${m}-${da}`
  }
  if (preset === 'today') {
    const d = ymd(today)
    return { from: d, to: d, upcoming: true }
  }
  if (preset === 'tomorrow') {
    const t = new Date(today); t.setDate(t.getDate() + 1)
    const d = ymd(t)
    return { from: d, to: d, upcoming: true }
  }
  if (preset === 'next_7') {
    const from = ymd(today)
    const t = new Date(today); t.setDate(t.getDate() + 7)
    return { from, to: ymd(t), upcoming: true }
  }
  if (preset === 'next_30') {
    const from = ymd(today)
    const t = new Date(today); t.setDate(t.getDate() + 30)
    return { from, to: ymd(t), upcoming: true }
  }
  return { from: undefined, to: undefined, upcoming: true }
}

const blank = (v) => (v === '' || v == null ? undefined : v)

async function applyFilters() {
  const range = mapDatePreset(form.datePreset)
  await store.fetchList({
    page: 1,
    q: blank(form.q),
    city: blank(store.filters.city),
    type: blank(form.type),
    mode: blank(form.mode),
    from: range.from,
    to: range.to,
    upcoming: range.upcoming
  })
}
</script>

<style scoped>
.fx-btn {
  @apply inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-zinc-300 dark:border-white/10 bg-white dark:bg-transparent hover:bg-zinc-50 dark:hover:bg-white/5;
}
.fx-menu {
  @apply fixed z-[9999] min-w-[220px] rounded-xl border border-zinc-200 bg-white p-1 shadow-lg dark:border-white/10 dark:bg-[#0b0f1a];
}
.fx-item {
  @apply w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5;
}
</style>
