<template>
  <section class="w-full" dir="ltr">
    <!-- Menu Tabs -->
    <nav
        class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-3 mb-4"
    >
      <div class="flex flex-wrap gap-2">
        <button
            v-for="item in menuItems"
            :key="item.key"
            @click="setActiveTab(item.key)"
            class="px-4 py-2 rounded-full text-sm font-medium transition-all
                 text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400
                 hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{
            'bg-gray-100 dark:bg-gray-800 text-red-600 dark:text-red-400': props.activeTab === item.key
          }"
        >
          {{ $t(item.label) }}
        </button>
      </div>

      <!-- Search Input -->
      <div class="relative">
        <input
            type="text"
            v-model="searchQuery"
            :placeholder="$t('politicalProfile.profileMenu.search')"
            class="pl-10 pr-4 py-2 w-64 rounded-full text-sm bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 text-gray-700 dark:text-gray-200"
        />
        <Icon
            icon="mdi:magnify"
            class="absolute left-3 top-2.5 w-5 h-5 text-gray-400 dark:text-gray-500"
        />
      </div>
    </nav>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  activeTab: String,
})

const emit = defineEmits(['update:activeTab'])

const searchQuery = ref('')

const menuItems = [
  { key: 'posts', label: 'politicalProfile.profileMenu.posts' },
  { key: 'about', label: 'politicalProfile.profileMenu.about' },
  { key: 'videos', label: 'politicalProfile.profileMenu.videos' },
  { key: 'photos', label: 'politicalProfile.profileMenu.photos' },
  { key: 'files', label: 'politicalProfile.profileMenu.files' },
  { key: 'contact', label: 'politicalProfile.profileMenu.contact' },
]

const setActiveTab = (key) => {
  emit('update:activeTab', key)
}
</script>
