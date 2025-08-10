<template>
  <nav class="flex flex-col gap-1 p-4 h-full">
    <router-link
        v-for="item in menu"
        :key="item.name"
        :to="item.to"
        class="flex items-center gap-3 px-4 py-2 rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700"
        active-class="bg-gray-200 dark:bg-gray-700 font-semibold"
    >
      <Icon :icon="item.icon" class="w-5 h-5" />
      <span class="text-sm font-medium">
        {{ $t(`dashboard.${item.name}`) }}
      </span>
    </router-link>
  </nav>
</template>

<script setup>
import { Icon } from '@iconify/vue'

const menu = [

  { name: 'dashboard_home', to: '/dashboard/dashboard-home', icon: 'mdi:view-dashboard-outline' },
  { name: 'new_post', to: '/dashboard/new-post', icon: 'mdi:pencil-plus-outline' },
  { name: 'publications', to: '/dashboard/publications', icon: 'mdi:book-open-variant' },

  // { name: 'drafts', to: '/dashboard/drafts', icon: 'mdi:file-document-edit-outline' },
  // { name: 'scheduled', to: '/dashboard/scheduled', icon: 'mdi:calendar-clock' },
  // { name: 'media_library', to: '/dashboard/media-library', icon: 'mdi:image-multiple-outline' },

  // { name: 'campaigns', to: '/dashboard/campaigns', icon: 'mdi:bullhorn-outline' },
  { name: 'dashboard_events', to: '/dashboard/events', icon: 'mdi:calendar-star' },
  // { name: 'announcements', to: '/dashboard/announcements', icon: 'mdi:megaphone-outline' },

  // { name: 'templates', to: '/dashboard/templates', icon: 'mdi:file-document-multiple-outline' },
  // { name: 'moderation', to: '/dashboard/moderation', icon: 'mdi:shield-alert-outline' },

  { name: 'my_posts', to: '/dashboard/myposts', icon: 'mdi:file-document-outline' },
  { name: 'archive', to: '/dashboard/archive', icon: 'mdi:archive-outline' },
  { name: 'political_profile', to: '/dashboard/political-profile', icon: 'mdi:domain' },
  { name: 'settings', to: '/dashboard/settings', icon: 'mdi:cog-outline' },
]
</script>
