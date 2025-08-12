<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
      <h1 class="text-xl font-bold">Type change requests</h1>
      <div class="ml-auto flex items-center gap-2">
        <input v-model="q" type="search" placeholder="Search name/email/slug" class="rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent w-64">
        <select v-model="requested" class="rounded-xl border px-3 py-2 text-sm dark:border-white/10 dark:bg-transparent">
          <option value="">All</option>
          <option value="party">Party</option>
          <option value="collective">Collective</option>
          <option value="media">Media</option>
        </select>
        <button @click="reload" :disabled="loading" class="rounded-xl border px-3 py-2 text-sm dark:border-white/10">Refresh</button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-zinc-50 text-zinc-600 dark:bg-white/5 dark:text-zinc-300">
        <tr>
          <th class="px-4 py-3">User</th>
          <th class="px-4 py-3">Current</th>
          <th class="px-4 py-3">Requested</th>
          <th class="px-4 py-3">Group</th>
          <th class="px-4 py-3">Location</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="r in list" :key="r.id" class="border-t border-zinc-100 hover:bg-zinc-50/60 dark:border-white/10 dark:hover:bg-white/5">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 shrink-0 rounded-full ring-1 ring-inset ring-zinc-200 dark:ring-white/10" :style="{ background: r.avatar_color || '#f3f4f6' }"></div>
              <div class="min-w-0">
                <div class="truncate font-medium text-zinc-900 dark:text-white">{{ r.user?.name }}</div>
                <div class="truncate text-xs text-zinc-500">{{ r.user?.email }}</div>
              </div>
            </div>
          </td>
          <td class="px-4 py-3">
            <span class="rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset" :class="pill(r.current_type)">{{ label(r.current_type) }}</span>
          </td>
          <td class="px-4 py-3">
            <span class="rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset" :class="pill(r.requested_type)">{{ label(r.requested_type) }}</span>
          </td>
          <td class="px-4 py-3">
            <span class="truncate block max-w-[220px]">{{ r.group_name || '—' }}</span>
          </td>
          <td class="px-4 py-3">
            <span class="truncate block max-w-[160px] text-xs text-zinc-500">{{ r.location || '—' }}</span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2">
              <button @click="approve(r.user.id)" :disabled="acting" class="rounded-xl border px-3 py-1.5 text-sm text-emerald-700 hover:bg-emerald-50 dark:border-white/10 dark:hover:bg-emerald-500/10">Approve</button>
              <button @click="reject(r.user.id)" :disabled="acting" class="rounded-xl border px-3 py-1.5 text-sm text-rose-600 hover:bg-rose-50 dark:border-white/10 dark:hover:bg-rose-500/10">Reject</button>
            </div>
          </td>
        </tr>
        <tr v-if="!list.length && !loading">
          <td colspan="6" class="px-4 py-10 text-center text-zinc-500">No pending requests</td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between">
      <div class="text-xs text-zinc-500">Page {{ page }} of {{ lastPage }} • Total {{ total }}</div>
      <div class="flex items-center gap-2">
        <button @click="prev" :disabled="page<=1 || loading" class="rounded-xl border px-3 py-1.5 text-sm dark:border-white/10">Prev</button>
        <button @click="next" :disabled="page>=lastPage || loading" class="rounded-xl border px-3 py-1.5 text-sm dark:border-white/10">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useTypeRequestsStore } from '@/stores/admin/users/typeRequests.store'

const store = useTypeRequestsStore()
const loading = computed(() => store.loading)
const acting = computed(() => store.acting)
const list = computed(() => store.list)
const page = computed(() => store.pagination.page)
const lastPage = computed(() => store.pagination.lastPage)
const total = computed(() => store.pagination.total)

const q = ref(store.filters.q)
const requested = ref(store.filters.requested)

function reload() {
  store.setFilters({ q: q.value, requested: requested.value })
  store.fetchList({ page: 1 })
}
watch([q, requested], () => {}, { flush: 'post' })

function next() { store.setPage(page.value + 1); store.fetchList() }
function prev() { store.setPage(page.value - 1); store.fetchList() }

function label(t) {
  if (!t) return '—'
  if (t === 'individual') return 'Individual'
  if (t === 'party') return 'Party'
  if (t === 'collective') return 'Collective'
  if (t === 'media') return 'Media'
  return t
}
function pill(t) {
  if (t === 'individual') return 'bg-zinc-100 text-zinc-700 ring-zinc-200 dark:bg-white/10 dark:text-zinc-300'
  if (t === 'party') return 'bg-rose-500/15 text-rose-700 ring-rose-500/20 dark:text-rose-300'
  if (t === 'collective') return 'bg-amber-500/15 text-amber-700 ring-amber-500/20 dark:text-amber-300'
  if (t === 'media') return 'bg-sky-500/15 text-sky-700 ring-sky-500/20 dark:text-sky-300'
  return 'bg-zinc-100 text-zinc-700 ring-zinc-200 dark:bg-white/10 dark:text-zinc-300'
}

async function approve(userId) {
  await store.approve(userId)
}
async function reject(userId) {
  await store.reject(userId)
}

reload()
</script>
