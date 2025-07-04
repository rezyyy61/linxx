<template>
    <nav class="flex flex-col gap-1 p-4 h-full">
        <router-link
            v-for="item in menu"
            :key="item.name"
            :to="item.to"
            class="flex items-center gap-3 px-4 py-2 rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700"
            active-class="bg-gray-200 dark:bg-gray-700 font-semibold"
        >
            <component :is="item.icon" class="w-5 h-5" />
            <span class="text-sm font-medium">
        {{ $t(`dashboard.${item.name}`) }}
      </span>
        </router-link>
    </nav>
</template>

<script setup>
import {
    Archive,
    FileEdit,
    Home,
    PenSquare,
    Landmark,
    Settings
} from 'lucide-vue-next'

const menu = [
    { name: 'dashboard_home', to: '/dashboard/dashboard-home', icon: Home },
    { name: 'new_post', to: '/dashboard/new-post', icon: PenSquare },
    { name: 'archive', to: '/dashboard/archive', icon: Archive },
    { name: 'my_posts', to: '/dashboard/myposts', icon: FileEdit },
    { name: 'political_profile', to: '/dashboard/political-profile', icon: Landmark },
    { name: 'settings', to: '/dashboard/settings', icon: Settings }
]
</script>
