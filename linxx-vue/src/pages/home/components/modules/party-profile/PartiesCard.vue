<template>
  <article
      class="relative flex w-full overflow-hidden rounded-2xl border shadow transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-700 dark:shadow-none group"
      :style="{ borderColor: accent, boxShadow: `0 0 0 1px ${accent}33` }"
  >
    <span class="absolute inset-y-0 left-0 w-1" :style="{ background: accent }" />

    <div class="flex flex-1 flex-col bg-white/90 dark:bg-gray-800/80 backdrop-blur">
      <header class="flex items-start justify-between gap-4 p-4 flex-wrap sm:flex-nowrap">
        <img
            :src="logoUrl"
            :alt="profile.group_name"
            class="w-16 h-16 rounded-full object-cover border border-gray-300 dark:border-gray-600 shrink-0"
        />

        <div class="flex-1 min-w-0 self-center">
          <h3 class="font-semibold text-gray-900 dark:text-gray-50 text-base sm:text-lg truncate">
            {{ profile.group_name }}
          </h3>
          <p
              v-if="safe(profile.tagline)"
              class="mt-0.5 text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate"
          >
            {{ profile.tagline }}
          </p>

          <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-gray-600 dark:text-gray-300">
            <template v-for="link in socialLinks" :key="link.label + (link.url || '')">
              <span
                  v-if="link.url"
                  class="inline-flex items-center gap-1 text-xs sm:text-sm hover:text-red-500 dark:hover:text-red-400 transition"
              >
                <Icon :icon="link.icon" class="w-4 h-4 shrink-0" />
                <a :href="link.url" target="_blank" rel="noopener">{{ link.label }}</a>
              </span>
              <span
                  v-else
                  class="inline-flex items-center gap-1 text-xs sm:text-sm text-gray-400 cursor-not-allowed"
              >
                <Icon :icon="link.icon" class="w-4 h-4 shrink-0" />
                <span>{{ link.label }}</span>
              </span>
            </template>
          </div>
        </div>

        <span
            class="whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] sm:text-xs font-medium capitalize select-none self-start"
            :style="{ background: accent + '20', color: accent }"
        >
          {{ profile.entity_type }}
        </span>
      </header>

      <p v-if="brief" class="px-4 pb-3 text-xs sm:text-sm text-gray-700 dark:text-gray-300 line-clamp-4">
        {{ brief }}
      </p>

      <footer
          class="mt-auto flex items-center justify-between gap-2 px-4 pb-4 text-[11px] sm:text-xs text-gray-400 dark:text-gray-500"
      >
        <span v-if="safe(profile.location)" class="inline-flex items-center gap-1 truncate">
          <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 119.5 9 2.5 2.5 0 0112 11.5z" />
          </svg>
          {{ profile.location }}
        </span>
        <span v-if="profile.founded_year" class="truncate">Founded {{ profile.founded_year }}</span>
      </footer>
    </div>

    <router-link
        v-if="slug"
        :to="{ name: 'public.profile', params: { slug } }"
        class="absolute inset-0"
    />
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({ profile: { type: Object, required: true } })
const accent = '#EF4444'

function safe(v) {
  return v && v !== 'null' && v !== 'undefined'
}

const slug = computed(() => props.profile?.user?.slug || props.profile?.slug || null)

const logoUrl = computed(() => {
  if (safe(props.profile?.logo_url)) return props.profile.logo_url
  if (safe(props.profile?.logo_path)) {
    const base = import.meta.env.VITE_API_BASE_URL?.replace(/\/+$/, '') || ''
    return `${base}/storage/${props.profile.logo_path}`
  }
  return 'https://placehold.co/128x128?text=Logo'
})

const brief = computed(() => {
  const first = safe(props.profile?.about) ? props.profile.about : null
  const second = safe(props.profile?.goals) ? props.profile.goals : null
  return first || second || ''
})

const socialLinks = computed(() => {
  const p = props.profile || {}

  const base = [
    { key: 'website', icon: 'mdi:web', label: 'Website' },
    { key: 'email', icon: 'mdi:email', label: 'Email' },
    { key: 'phone', icon: 'mdi:phone', label: 'Phone' },
    { key: 'twitter', icon: 'mdi:twitter', label: 'Twitter' },
    { key: 'facebook', icon: 'mdi:facebook', label: 'Facebook' },
    { key: 'instagram', icon: 'mdi:instagram', label: 'Instagram' },
    { key: 'telegram', icon: 'mdi:telegram', label: 'Telegram' }
  ]

  const map = Object.fromEntries(
          base.map(({ key, icon, label }) => [
            label.toLowerCase(),
            { icon, label, url: safe(p[key]) ? p[key] : null }
          ])
      )

  ;(Array.isArray(p.links) ? p.links : [])
      .filter(l => safe(l.url || l.link))
      .forEach(l => {
        const url = l.url || l.link
        const t = (l.title || l.name || '').trim().toLowerCase()
        if (map[t]) {
          map[t].url = url
        } else {
          map[`custom-${url}`] = { icon: 'mdi:link', label: l.title || l.name || 'Link', url }
        }
      })

  return Object.values(map)
})
</script>

<style scoped>
.line-clamp-4 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 4;
  overflow: hidden;
}
</style>
