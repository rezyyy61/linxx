<template>
  <header class="rounded-2xl bg-gradient-to-r from-fuchsia-500 via-rose-500 to-orange-500 p-[1px] shadow-xl">
    <div class="rounded-2xl bg-white dark:bg-[#0b0f1a] px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-rose-100 dark:bg-rose-500/15 grid place-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-600 dark:text-rose-300" viewBox="0 0 24 24" fill="currentColor"><path d="M6.25 2A2.25 2.25 0 0 0 4 4.25v15.5A2.25 2.25 0 0 0 6.25 22h11.5A2.25 2.25 0 0 0 20 19.75V8.621a2.25 2.25 0 0 0-.659-1.591l-4.37-4.37A2.25 2.25 0 0 0 13.379 2H6.25zM6.5 6.75A.75.75 0 0 1 7.25 6h5.5a.75.75 0  1 1 0 1.5h-5.5A.75.75 0 0 1 6.5 6.75zM6.5 10.5A.75.75 0 0 1 7.25 9.75h9.5a.75.75 0 0 1 0 1.5h-9.5a.75.75 0 0 1-.75-.75zM6.5 14.25a.75.75 0 0 1 .75-.75h9.5a.75.75 0 1 1 0 1.5h-9.5a.75.75 0 0 1-.75-.75zM6.5 18a.75.75 0 0 1 .75-.75h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 6.5 18z"/></svg>
        </div>
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $t('dashboard.event.header.title') }}
          </h1>
          <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ $t('dashboard.event.header.subtitle') }}
          </p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
        <div class="relative flex-1 sm:flex-none">
          <input
              :value="searchQuery"
              @input="$emit('update:searchQuery', $event.target.value)"
              type="search"
              :placeholder="$t('dashboard.event.search.placeholder')"
              :dir="isRTL ? 'rtl' : 'ltr'"
              class="w-full sm:w-64 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#0e1526] text-zinc-900 dark:text-zinc-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500/70 dark:focus:ring-fuchsia-500/60 placeholder:text-zinc-400 dark:placeholder:text-zinc-500 transition"
              :class="isRTL ? 'pl-10' : 'pr-10'"
          />
          <svg
              class="pointer-events-none absolute top-1/2 -translate-y-1/2 h-5 w-5 text-zinc-400 dark:text-zinc-500"
              :class="isRTL ? 'left-3' : 'right-3'"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
          </svg>
        </div>

        <select
            :value="statusFilter"
            @change="$emit('update:statusFilter', $event.target.value)"
            :dir="isRTL ? 'rtl' : 'ltr'"
            class="rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#0e1526] text-zinc-900 dark:text-zinc-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500/70 dark:focus:ring-fuchsia-500/60 transition"
        >
          <option value="all">{{ $t('dashboard.event.filters.status.all') }}</option>
          <option value="active">{{ $t('dashboard.event.filters.status.active') }}</option>
          <option value="draft">{{ $t('dashboard.event.filters.status.draft') }}</option>
          <option value="cancelled">{{ $t('dashboard.event.filters.status.cancelled') }}</option>
        </select>

        <button
            @click="$emit('create')"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 font-medium text-white shadow-sm hover:bg-rose-700 active:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500/60 transition dark:bg-fuchsia-600 dark:hover:bg-fuchsia-700 dark:active:bg-fuchsia-800"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5H12.75v6.75a.75.75 0 0 1-1.5 0V12.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5A.75.75 0 0 1 12 3.75z" clip-rule="evenodd"/></svg>
          {{ $t('dashboard.event.actions.new') }}
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

defineProps({
  searchQuery: { type: String, default: '' },
  statusFilter: { type: String, default: 'all' }
})
defineEmits(['update:searchQuery', 'update:statusFilter', 'create'])

const { locale } = useI18n({ useScope: 'global' })
const isRTL = computed(() => {
  const l = String(locale?.value || '').toLowerCase()
  const base = l.split('-')[0]
  if (['fa', 'ar', 'he', 'ur', 'ps', 'ku'].includes(base)) return true
  if (typeof document !== 'undefined') {
    const dir = (document.documentElement.getAttribute('dir') || document.dir || '').toLowerCase()
    if (dir) return dir === 'rtl'
  }
  return false
})
</script>
