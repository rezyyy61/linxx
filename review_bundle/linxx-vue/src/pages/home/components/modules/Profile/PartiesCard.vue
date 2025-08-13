<template>
  <article class="relative w-full overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid grid-cols-1 md:grid-cols-[112px,1fr,auto] gap-4 p-4">
      <!-- Logo box -->
      <div class="md:row-span-2">
        <div class="relative w-24 h-24 md:w-28 md:h-28 rounded-xl ring-1 ring-inset ring-zinc-200 bg-white flex items-center justify-center overflow-hidden dark:ring-zinc-700">
          <div v-if="!imgLoaded && hasLogo" class="absolute inset-0 animate-pulse bg-zinc-100 dark:bg-zinc-800"></div>

          <img
              v-if="hasLogo && !imgError"
              :src="logoUrl"
              :alt="`${title} logo`"
              class="max-w-full max-h-full object-contain p-2"
              loading="lazy"
              decoding="async"
              @load="imgLoaded = true"
              @error="imgError = true"
          />

          <div
              v-if="!hasLogo || imgError"
              class="w-full h-full flex items-center justify-center text-lg md:text-xl font-bold uppercase bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
          >
            {{ initials }}
          </div>
        </div>
      </div>

      <!-- Title + tagline -->
      <div class="min-w-0">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <h3 class="text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-50 truncate">
              {{ title }}
            </h3>
            <p v-if="tagline" class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400 truncate">
              {{ tagline }}
            </p>
            <div v-else class="mt-1 h-3 w-40 rounded bg-zinc-100 dark:bg-zinc-800" />
          </div>

          <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[11px] md:text-xs font-medium capitalize text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:text-zinc-300 dark:ring-zinc-700">
            {{ typeLabel }}
          </span>
        </div>

        <!-- Socials -->
        <div v-if="socials.length" class="mt-3 flex flex-wrap items-center gap-2">
          <a
              v-for="s in socials"
              :key="s.key"
              :href="s.url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center justify-center w-8 h-8 rounded-full ring-1 ring-inset ring-zinc-200 text-zinc-700 hover:bg-zinc-50 dark:ring-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
              :aria-label="s.label"
              :title="s.label"
          >
            <Icon :icon="s.icon" class="w-4 h-4" />
          </a>
        </div>
      </div>

      <!-- CTA -->
      <div class="md:row-span-2 flex md:flex-col items-end justify-between gap-3">
        <RouterLink
            v-if="linkTo"
            :to="linkTo"
            class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium ring-1 ring-inset ring-zinc-200 text-zinc-800 hover:bg-zinc-50 dark:text-zinc-100 dark:ring-zinc-700 dark:hover:bg-zinc-800"
        >
          {{ t('viewProfile','View profile') }}
          <Icon icon="mdi:arrow-right" class="w-4 h-4" />
        </RouterLink>
      </div>

      <!-- Brief -->
      <div class="md:col-span-2 text-sm text-zinc-700 dark:text-zinc-300">
        <p v-if="brief" :class="expanded ? '' : 'line-clamp-3'">{{ brief }}</p>
        <div v-else class="space-y-2">
          <div class="h-3 w-11/12 rounded bg-zinc-100 dark:bg-zinc-800"></div>
          <div class="h-3 w-8/12 rounded bg-zinc-100 dark:bg-zinc-800"></div>
        </div>

        <button
            v-if="briefTooLong"
            @click="expanded = !expanded"
            type="button"
            class="mt-1 text-xs text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
        >
          {{ expanded ? t('showLess','Show less') : t('showMore','Show more') }}
        </button>
      </div>

      <!-- Meta -->
      <div class="md:col-span-2 flex flex-wrap items-center gap-2 text-[11px] md:text-xs">
        <span v-if="location" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 ring-1 ring-inset ring-zinc-200 text-zinc-600 dark:text-zinc-300 dark:ring-zinc-700">
          <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 119.5 9 2.5 2.5 0 0112 11.5z" /></svg>
          {{ location }}
        </span>
        <span v-if="founded" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 ring-1 ring-inset ring-zinc-200 text-zinc-600 dark:text-zinc-300 dark:ring-zinc-700">
          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 11h10v2H7z" /></svg>
          {{ t('founded','Founded') }} {{ founded }}
        </span>
      </div>
    </div>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'

const props = defineProps({ profile: { type: Object, required: true } })
const { t } = useI18n()

const imgError = ref(false)
const imgLoaded = ref(false)
const expanded = ref(false)

const norm = (v) => {
  if (v === null || v === undefined) return ''
  const s = String(v).trim()
  return s.toLowerCase() === 'null' || s.toLowerCase() === 'undefined' ? '' : s
}

const title = computed(() => norm(props.profile?.group_name) || norm(props.profile?.user?.name) || norm(props.profile?.user?.slug) || '—')
const slug = computed(() => norm(props.profile?.user?.slug) || norm(props.profile?.slug))
const linkTo = computed(() => (slug.value ? `/${slug.value}` : null))

const typeLabel = computed(() => (norm(props.profile?.entity_type) || '—').toLowerCase())

const initials = computed(() => {
  const s = title.value
  const parts = s.split(/\s+/).filter(Boolean)
  return (parts.length === 1 ? parts[0].slice(0, 2) : (parts[0][0] + parts[1][0])).toUpperCase()
})

const logoUrl = computed(() => {
  const lu = norm(props.profile?.logo_url)
  if (lu) return lu
  const lp = norm(props.profile?.logo_path)
  if (lp) {
    const base = import.meta.env.VITE_API_BASE_URL?.replace(/\/+$/, '') || ''
    return `${base}/storage/${lp}`
  }
  return ''
})
const hasLogo = computed(() => !!logoUrl.value)

const tagline = computed(() => norm(props.profile?.tagline))
const location = computed(() => norm(props.profile?.location))
const founded = computed(() => norm(props.profile?.founded_year))
const brief = computed(() => norm(props.profile?.about) || norm(props.profile?.goals))
const briefTooLong = computed(() => brief.value.length > 160)

const socials = computed(() => {
  const p = props.profile || {}
  const out = []
  const pushIf = (key, icon, label) => { const url = norm(p[key]); if (url) out.push({ key: `${key}-${url}`, icon, label, url }) }
  pushIf('website', 'mdi:web', 'Website')
  pushIf('email', 'mdi:email', 'Email')
  pushIf('phone', 'mdi:phone', 'Phone')
  pushIf('twitter', 'mdi:twitter', 'Twitter')
  pushIf('facebook', 'mdi:facebook', 'Facebook')
  pushIf('instagram', 'mdi:instagram', 'Instagram')
  pushIf('telegram', 'mdi:telegram', 'Telegram')
  const extra = Array.isArray(p.links) ? p.links : []
  extra.forEach((l, i) => { const url = norm(l?.url || l?.link); const label = norm(l?.title || l?.name || 'Link'); if (url) out.push({ key: `ext-${i}-${url}`, icon: 'mdi:link', label, url }) })
  return out.slice(0, 6)
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  overflow: hidden;
}
</style>
