<template>
  <div class="w-full">
    <!-- 모바일: 카드 뷰 -->
    <div class="space-y-3 md:hidden">
      <div
          v-for="u in items"
          :key="u.id"
          class="rounded-2xl border border-zinc-200 p-4 dark:border-white/10 dark:bg-white/5"
      >
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 shrink-0 rounded-full ring-1 ring-inset ring-zinc-200 overflow-hidden relative dark:ring-white/10">
            <img
                v-if="avatarSrc(u) && !broken[u.id]"
                :src="avatarSrc(u)"
                :alt="u.name || u.slug || 'avatar'"
                class="h-full w-full object-cover"
                loading="lazy"
                decoding="async"
                @error="onImgError(u.id)"
            />
            <div
                v-else
                class="h-full w-full flex items-center justify-center text-[12px] font-semibold uppercase"
                :style="{ background: avatarColor(u), color: fgColor(avatarColor(u)) }"
            >
              {{ initials(u) }}
            </div>
          </div>

          <div class="min-w-0 flex-1">
            <div class="truncate font-medium text-zinc-900 dark:text-white">{{ u.name || '—' }}</div>
            <div class="truncate text-xs text-zinc-500">ID: {{ u.id }}</div>
          </div>
        </div>

        <div class="mt-3 space-y-1 text-sm">
          <div class="flex items-center justify-between">
            <span class="text-zinc-500">Email</span>
            <span class="max-w-[60%] truncate text-right">{{ u.email || '—' }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-zinc-500">Slug</span>
            <span class="rounded bg-zinc-100 px-2 py-0.5 text-xs dark:bg-white/10">{{ u.slug || '—' }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-zinc-500">UUID</span>
            <span class="max-w-[60%] truncate text-right text-xs text-zinc-600">{{ u.uuid || '—' }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-zinc-500">Verified</span>
            <span v-if="u.is_verified" class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:text-emerald-300">Yes</span>
            <span v-else class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300">No</span>
          </div>
          <div class="pt-1">
            <div class="font-medium">
              {{ u.profile?.group_name || '—' }}
              <span v-if="u.profile?.tagline" class="ml-1 text-xs text-zinc-500">— {{ u.profile.tagline }}</span>
            </div>
            <div v-if="u.profile?.location" class="text-xs text-zinc-500">{{ u.profile.location }}</div>
          </div>
          <div class="pt-1">
            <span class="rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset" :class="typePillClass(u.profile?.entity_type)">
              {{ labelType(u.profile?.entity_type) }}
            </span>
          </div>
        </div>

        <div class="mt-3 flex items-center justify-end gap-2">
          <button @click="$emit('edit', u)" class="rounded-xl border px-3 py-1.5 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">Edit</button>
          <button @click="$emit('delete', u)" class="rounded-xl border px-3 py-1.5 text-sm text-rose-600 hover:bg-rose-50 dark:border-white/10 dark:hover:bg-rose-500/10">Delete</button>
        </div>
      </div>

      <div v-if="!items.length" class="rounded-2xl border border-dashed border-zinc-200 p-10 text-center text-zinc-500 dark:border-white/10">
        No users
      </div>
    </div>

    <!-- 데스크톱: 테이블 뷰 -->
    <div class="overflow-x-auto hidden md:block">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-zinc-50 text-zinc-600 dark:bg-white/5 dark:text-zinc-300">
        <tr>
          <th class="px-4 py-3">User</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Slug</th>
          <th class="px-4 py-3">UUID</th>
          <th class="px-4 py-3">Verified</th>
          <th class="px-4 py-3">Profile</th>
          <th class="px-4 py-3">Type</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="u in items" :key="u.id" class="border-t border-zinc-100 hover:bg-zinc-50/60 dark:border-white/10 dark:hover:bg-white/5">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 shrink-0 rounded-full ring-1 ring-inset ring-zinc-200 overflow-hidden relative dark:ring-white/10">
                <img
                    v-if="avatarSrc(u) && !broken[u.id]"
                    :src="avatarSrc(u)"
                    :alt="u.name || u.slug || 'avatar'"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                    @error="onImgError(u.id)"
                />
                <div
                    v-else
                    class="h-full w-full flex items-center justify-center text-[11px] font-semibold uppercase"
                    :style="{ background: avatarColor(u), color: fgColor(avatarColor(u)) }"
                >
                  {{ initials(u) }}
                </div>
              </div>
              <div class="min-w-0">
                <div class="truncate font-medium text-zinc-900 dark:text-white">{{ u.name }}</div>
                <div class="truncate text-xs text-zinc-500">ID: {{ u.id }}</div>
              </div>
            </div>
          </td>
          <td class="px-4 py-3">
            <span class="truncate block max-w-[220px]">{{ u.email }}</span>
          </td>
          <td class="px-4 py-3">
            <span class="rounded-lg bg-zinc-100 px-2 py-0.5 text-xs dark:bg-white/10">{{ u.slug }}</span>
          </td>
          <td class="px-4 py-3">
            <span class="truncate block max-w-[180px] text-xs text-zinc-500">{{ u.uuid }}</span>
          </td>
          <td class="px-4 py-3">
            <span v-if="u.is_verified" class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:text-emerald-300">Yes</span>
            <span v-else class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300">No</span>
          </td>
          <td class="px-4 py-3">
            <div class="truncate max-w-[260px]">
              <span class="font-medium">{{ u.profile?.group_name || '—' }}</span>
              <span v-if="u.profile?.tagline" class="text-xs text-zinc-500">— {{ u.profile.tagline }}</span>
              <div v-if="u.profile?.location" class="text-xs text-zinc-500">{{ u.profile.location }}</div>
            </div>
          </td>
          <td class="px-4 py-3">
              <span class="rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset" :class="typePillClass(u.profile?.entity_type)">
                {{ labelType(u.profile?.entity_type) }}
              </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2">
              <button @click="$emit('edit', u)" class="rounded-xl border px-3 py-1.5 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">Edit</button>
              <button @click="$emit('delete', u)" class="rounded-xl border px-3 py-1.5 text-sm text-rose-600 hover:bg-rose-50 dark:border-white/10 dark:hover:bg-rose-500/10">Delete</button>
            </div>
          </td>
        </tr>
        <tr v-if="!items.length">
          <td colspan="8" class="px-4 py-10 text-center text-zinc-500">No users</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import {reactive} from 'vue'

const props = defineProps({
  items: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false }
})

const broken = reactive({})

function onImgError(id) { broken[id] = true }

function avatarSrc(u) {
  return u?.profile?.logo_url || null
}

function avatarColor(u) {
  return u?.profile?.avatar_color || '#f3f4f6'
}

function initials(u) {
  const txt = (u?.name || u?.slug || '').trim()
  if (!txt) return '?'
  const parts = txt.split(/\s+/).filter(Boolean)
  if (parts.length === 1) return parts[0].slice(0, 2)
  return (parts[0][0] + parts[1][0]).toUpperCase()
}

function fgColor(bg) {
  const hex = (bg || '').replace('#', '')
  if (!/^[0-9a-f]{6}$/i.test(hex)) return '#111827'
  const r = parseInt(hex.slice(0, 2), 16)
  const g = parseInt(hex.slice(2, 4), 16)
  const b = parseInt(hex.slice(4, 6), 16)
  const yiq = (r * 299 + g * 587 + b * 114) / 1000
  return yiq >= 150 ? '#111827' : '#ffffff'
}

function labelType(t) {
  if (!t) return '—'
  if (t === 'individual') return 'Individual'
  if (t === 'party') return 'Party'
  if (t === 'collective') return 'Collective'
  if (t === 'media') return 'Media'
  return t
}
function typePillClass(t) {
  if (t === 'individual') return 'bg-zinc-100 text-zinc-700 ring-zinc-200 dark:bg-white/10 dark:text-zinc-300'
  if (t === 'party') return 'bg-rose-500/15 text-rose-700 ring-rose-500/20 dark:text-rose-300'
  if (t === 'collective') return 'bg-amber-500/15 text-amber-700 ring-amber-500/20 dark:text-amber-300'
  if (t === 'media') return 'bg-sky-500/15 text-sky-700 ring-sky-500/20 dark:text-sky-300'
  return 'bg-zinc-100 text-zinc-700 ring-zinc-200 dark:bg-white/10 dark:text-zinc-300'
}
</script>
