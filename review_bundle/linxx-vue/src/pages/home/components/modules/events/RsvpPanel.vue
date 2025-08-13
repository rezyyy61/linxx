<template>
  <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a]">
    <div class="font-medium mb-2">{{ t('home.event.rsvp.title') }}</div>

    <div class="flex flex-wrap items-center gap-2">
      <button
          class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 disabled:opacity-50 dark:border-white/10 dark:hover:bg-white/5"
          :disabled="disabled || saving"
          :class="isGoing ? 'ring-1 ring-emerald-400/50' : ''"
          @click="onSet('going')"
          :aria-pressed="isGoing ? 'true' : 'false'"
      >
        <Icon icon="mdi:check" class="w-4 h-4" />
        <span>{{ t('home.event.rsvp.going') }}</span>
      </button>

      <button
          class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 disabled:opacity-50 dark:border-white/10 dark:hover:bg-white/5"
          :disabled="disabled || saving"
          :class="isInterested ? 'ring-1 ring-amber-400/50' : ''"
          @click="onSet('interested')"
          :aria-pressed="isInterested ? 'true' : 'false'"
      >
        <Icon icon="mdi:star-outline" class="w-4 h-4" />
        <span>{{ t('home.event.rsvp.interested') }}</span>
      </button>

      <button
          v-if="myStatus"
          class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 disabled:opacity-50 dark:border-white/10 dark:hover:bg-white/5"
          :disabled="disabled || saving"
          @click="onCancel"
      >
        <Icon icon="mdi:close" class="w-4 h-4" />
        <span>{{ t('home.event.rsvp.cancel') }}</span>
      </button>
    </div>

    <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
      <span>{{ counts.going }} {{ t('home.event.rsvp.countGoing') }}</span>
      <span class="mx-2">·</span>
      <span>{{ counts.interested }} {{ t('home.event.rsvp.countInterested') }}</span>
      <span v-if="capacity" class="ml-2">· {{ t('home.event.rsvp.capacity') }}: {{ capacity }}</span>
    </div>

    <div v-if="goingAvatars.length" class="mt-3 flex items-center gap-1">
      <component
          v-for="(a,i) in goingAvatars"
          :key="avatarKey(a,i)"
          :is="a.user?.slug ? 'RouterLink' : 'div'"
          :to="a.user?.slug ? { path: `/${a.user.slug}` } : undefined"
          class="h-8 w-8 rounded-full ring-2 ring-white dark:ring-[#0b0f1a] overflow-hidden -ml-1 first:ml-0 grid place-items-center cursor-pointer hover:brightness-105 transition"
          :title="a.user?.name || ''"
          :aria-label="a.user?.name || 'user'"
      >
        <img
            v-if="avatarUrl(a)"
            :src="avatarUrl(a)"
            class="h-full w-full object-cover"
            alt=""
        />
        <div
            v-else
            class="h-full w-full grid place-items-center text-[10px] font-semibold text-white"
            :style="{ background: avatarBg(a) }"
        >
          {{ initials(a?.user?.name) }}
        </div>
      </component>

      <div v-if="goingMore>0" class="ml-1 text-xs text-zinc-500 dark:text-zinc-400">+{{ goingMore }} more</div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import { useRsvpStore } from '@/stores/events/rsvp.store'

const props = defineProps({
  event: { type: Object, required: true },
  currentUserId: { type: [Number, String], default: null },
})

const { t } = useI18n({ useScope: 'global' })
const store = useRsvpStore()

const eventId = computed(() => props.event?.id || null)

const saving = computed(() => eventId.value ? store.isSaving(eventId.value) : false)
const counts = computed(() => eventId.value ? store.counts(eventId.value) : { going: 0, interested: 0 })
const capacity = computed(() => props.event?.rsvp?.capacity ?? null)

const disabled = computed(() => {
  const enabled = props.event?.rsvp?.enabled ?? true
  if (!enabled) return true
  const now = Date.now()
  const endsAt = props.event?.ends_at ? new Date(props.event.ends_at).getTime() : null
  const rsvpEnds = props.event?.rsvp?.ends_at ? new Date(props.event.rsvp.ends_at).getTime() : null
  const deadline = rsvpEnds ?? endsAt
  return deadline ? deadline < now : false
})

const attendeesList = computed(() => eventId.value ? store.attendees(eventId.value) : [])

const myStatusFromAttendees = computed(() => {
  const list = attendeesList.value || []
  const mine = list.find(x => x?.user?.is_current === true) ||
      (props.currentUserId ? list.find(x => x?.user?.id === props.currentUserId) : null)
  return mine?.status ?? null
})

const myStatus = computed(() => props.event?.rsvp?.my_status ?? myStatusFromAttendees.value)
const isGoing = computed(() => myStatus.value === 'going')
const isInterested = computed(() => myStatus.value === 'interested')

const goingAvatars = computed(() => eventId.value ? store.goingAvatars(eventId.value, 8) : [])
const goingMore = computed(() => eventId.value ? store.goingMoreCount(eventId.value, 8) : 0)

const avatarKey = (a, i) => {
  const uid = a?.user?.id ?? `nouser-${i}`
  const st = a?.status ?? 's'
  return `rsvp-${uid}-${st}-${i}`
}

// helpers for avatar rendering
const avatarUrl = (a) => a?.user?.logo_url || a?.user?.avatar_url || null
const avatarBg = (a) => a?.user?.avatar_color || '#9ca3af'
const initials = (name) => (name || 'U').trim().split(/\s+/).map(p => p[0]).slice(0, 2).join('').toUpperCase()

onMounted(async () => {
  if (!eventId.value) return
  await store.fetchAttendees(eventId.value)
  if (props.currentUserId != null) store.setCurrentUser(props.currentUserId)
})
watch(eventId, async (id) => { if (id) await store.fetchAttendees(id) })

async function onSet(status) {
  if (!eventId.value) return
  try {
    if (myStatus.value === status) {
      await store.cancel(eventId.value)
    } else {
      await store.setStatus(eventId.value, status)
    }
  } catch (e) {
    if (e?.status === 401) {
      window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`
    }
  }
}

async function onCancel() {
  if (!eventId.value) return
  try {
    await store.cancel(eventId.value)
  } catch (e) {
    if (e?.status === 401) {
      window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`
    }
  }
}
</script>
