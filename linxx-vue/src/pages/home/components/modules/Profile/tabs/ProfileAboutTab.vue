<template>
  <section class="w-full">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg w-full">
      <!-- Header Section -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ profile.group_name }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ profile.tagline }}</p>
      </div>

      <!-- Main Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left (Logo) -->
        <div class="lg:col-span-3 flex justify-center">
          <img
              :src="profile.logo_url"
              alt="logo"
              class="w-32 h-32 object-cover rounded-xl border border-gray-300 dark:border-gray-700"
          />
        </div>

        <!-- Right (Content) -->
        <div class="lg:col-span-9 space-y-6">
          <!-- About -->
          <div v-if="profile.about">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
              <Icon icon="mdi:information-outline" /> About
            </h2>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-6">{{ profile.about }}</p>
          </div>

          <!-- Goals -->
          <div v-if="profile.goals">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
              <Icon icon="mdi:bullseye-arrow" /> Goals
            </h2>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-6">{{ profile.goals }}</p>
          </div>

          <!-- Organization Info -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
              <div class="font-medium">Entity</div>
              <div>{{ profile.entity_type || '—' }}</div>
            </div>
            <div>
              <div class="font-medium">Structure</div>
              <div>{{ profile.structure || '—' }}</div>
            </div>
            <div>
              <div class="font-medium">Founded</div>
              <div>{{ profile.founded_year || '—' }}</div>
            </div>
            <div>
              <div class="font-medium">Location</div>
              <div>{{ profile.location || '—' }}</div>
            </div>
          </div>

          <!-- Ideologies -->
          <div v-if="profile.ideologies?.length">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
              <Icon icon="mdi:lightbulb-on-outline" /> Ideologies
            </h2>
            <div class="flex flex-wrap gap-2">
              <span
                  v-for="(item, index) in profile.ideologies"
                  :key="index"
                  class="px-3 py-1 rounded-full text-xs bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white"
              >
                {{ item.value }}
              </span>
            </div>
          </div>

          <!-- Keywords -->
          <div v-if="profile.keywords?.length">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
              <Icon icon="mdi:tag-multiple-outline" /> Keywords
            </h2>
            <div class="flex flex-wrap gap-2">
              <span
                  v-for="(item, index) in profile.keywords"
                  :key="index"
                  class="px-3 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-white"
              >
                {{ item }}
              </span>
            </div>
          </div>

          <!-- Files -->
          <div v-if="profile.files?.length">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
              <Icon icon="mdi:file-document-outline" /> Documents
            </h2>
            <ul class="space-y-2 text-sm">
              <li
                  v-for="file in profile.files"
                  :key="file.id"
                  class="flex items-center gap-2 hover:underline text-blue-600 dark:text-blue-400"
              >
                <Icon icon="mdi:file" class="w-4 h-4" />
                <a :href="file.url" target="_blank">{{ file.name }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Icon } from '@iconify/vue'
defineProps({ profile: Object })
</script>
