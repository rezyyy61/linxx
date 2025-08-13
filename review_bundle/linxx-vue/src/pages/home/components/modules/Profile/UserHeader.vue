<template>
  <div class="w-full px-4">
    <div
        v-if="user"
        class="group flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 p-6 sm:p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 hover:shadow-xl transition-all duration-300"
        dir="ltr"
    >
      <!-- Profile Info -->
      <div class="flex items-start gap-5 w-full sm:w-auto">
        <!-- Avatar -->
        <div class="shrink-0 self-start">
          <UserAvatar
              :src="user.logo_url"
              :fallback="user.group_name"
              :color="user.avatar_color"
              size="2xl"
          />
        </div>

        <!-- Info -->
        <div>
          <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
            {{ user.group_name }}
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ user.tagline }}
          </div>
        </div>
      </div>

      <!-- Social / Contact Links -->
      <div class="flex items-center flex-wrap gap-3 mt-4 sm:mt-0 text-sm">
        <a
            v-for="(item, index) in socialItems"
            :key="index"
            href="#"
            @click.prevent="handleClick(item.title)"
            class="flex items-center gap-2 px-3 py-1.5 rounded-full border transition
            border-gray-200 dark:border-gray-700
            bg-gray-100 dark:bg-gray-800
            hover:text-red-600 dark:hover:text-red-400
            hover:border-red-400"
            :class="getUserLink(item.title)
            ? 'text-gray-700 dark:text-white border-red-400'
            : 'text-gray-400 dark:text-gray-500 opacity-50 cursor-default'"
        >
          <Icon :icon="item.icon" class="w-4 h-4" />
          <span class="hidden sm:inline font-medium">{{ item.title }}</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import UserAvatar from '@/pages/auth/UserAvatar.vue'
import {watchEffect} from "vue";

const { user } = defineProps({
  user: Object
})

const socialItems = [
  { title: 'Website', icon: 'mdi:web' },
  { title: 'Twitter', icon: 'mdi:twitter' },
  { title: 'Facebook', icon: 'mdi:facebook' },
  { title: 'Instagram', icon: 'mdi:instagram' },
  { title: 'Telegram', icon: 'mdi:telegram' },
  { title: 'Email', icon: 'mdi:email-outline' },
  { title: 'Phone', icon: 'mdi:phone' }
]

const getUserLink = (title) => {
  return user?.links?.find(link => link.type?.toLowerCase() === title.toLowerCase())?.url || null
}

const handleClick = (type) => {
  const url = getUserLink(type)
  if (!url) return

  switch (type.toLowerCase()) {
    case 'website':
    case 'facebook':
    case 'twitter':
    case 'instagram':
    case 'telegram':
      window.open(url, '_blank')
      break

    case 'email':
      window.location.href = `mailto:${url}`
      break

    case 'phone':
      window.location.href = `tel:${url}`
      break

    default:
      window.open(url, '_blank')
  }
}

</script>
