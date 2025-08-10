<template>
  <div class="fixed inset-0 z-50 flex">
    <div class="flex-1 bg-black/40" @click="$emit('close')"></div>
    <div class="w-full max-w-md h-full bg-white dark:bg-[#0b0f1a] border-l border-zinc-200 dark:border-white/10 p-4 overflow-y-auto">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Attendees</h3>
        <button class="rounded-lg border px-2 py-1 text-sm dark:border-white/10" @click="$emit('close')">Close</button>
      </div>

      <div v-if="loading" class="space-y-2">
        <div v-for="n in 6" :key="n" class="h-10 bg-zinc-100 dark:bg-white/10 animate-pulse rounded"></div>
      </div>

      <div v-else-if="error" class="text-sm text-rose-600 dark:text-rose-400">
        {{ error }}
      </div>

      <div v-else>
        <div class="mb-3 text-sm text-zinc-600 dark:text-zinc-300">
          <b>{{ counts.going }}</b> Going · <b>{{ counts.interested }}</b> Interested
        </div>

        <div class="space-y-2">
          <div
              v-for="(it, i) in attendees"
              :key="it.user?.id || i"
              class="flex items-center gap-3 p-2 rounded-xl border dark:border-white/10"
          >
            <RouterLink
                :to="{ path: `/${it.user?.slug || ''}` }"
                class="shrink-0 h-10 w-10 rounded-full overflow-hidden ring-1 ring-black/5 dark:ring-white/10"
            >
              <img v-if="it.user?.logo_url" :src="it.user.logo_url" class="h-full w-full object-cover" alt="" />
              <div v-else class="h-full w-full grid place-items-center text-xs text-white font-semibold" :style="{ background: it.user?.avatar_color || '#9ca3af' }">
                {{ (it.user?.name || 'U').split(/\s+/).map(p=>p[0]).slice(0,2).join('').toUpperCase() }}
              </div>
            </RouterLink>

            <div class="min-w-0">
              <RouterLink :to="{ path: `/${it.user?.slug || ''}` }" class="block font-medium truncate hover:underline">
                {{ it.user?.name || '—' }}
              </RouterLink>
              <div class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ it.status }}
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { listAttendeesApi } from '@/stores/events/rsvp.api'

const props = defineProps({ event: { type: Object, required: true } })
const loading = ref(false)
const error = ref(null)
const attendees = ref([])

const counts = computed(() => {
  return attendees.value.reduce((a, it) => {
    if (it.status === 'going') a.going++
    else if (it.status === 'interested') a.interested++
    return a
  }, { going: 0, interested: 0 })
})

onMounted(async () => {
  loading.value = true
  try {
    const list = await listAttendeesApi(props.event.id)
    attendees.value = Array.isArray(list) ? list : []
  } catch (e) {
    error.value = e?.response?.data?.message || e?.message || 'Failed to load attendees'
  } finally {
    loading.value = false
  }
})
</script>
