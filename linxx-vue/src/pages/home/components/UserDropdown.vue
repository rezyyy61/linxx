<template>
    <div class="relative" v-click-outside="() => open = false">
        <!-- Avatar Button -->
        <button
            @click="open = !open"
            class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white hover:opacity-90 transition overflow-hidden shadow-md ring-1 ring-gray-200 dark:ring-gray-600"
        >
            <UserAvatar
                :src="user.avatar"
                :fallback="user.name"
                :color="user.political_profile?.avatar_color"
                size="lg"
            />

        </button>


        <!-- Dropdown Menu -->
        <div
            v-if="open"
            class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-xl z-50 overflow-hidden animate-fade"
        >
            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">👤 {{ user.name }}</div>
            <hr class="border-gray-200 dark:border-gray-700" />

            <router-link to="/profile" class="dropdown-item">Profile</router-link>
            <router-link to="/library" class="dropdown-item">Library</router-link>
            <router-link to="/stories" class="dropdown-item">Stories</router-link>
            <router-link to="/stats" class="dropdown-item">Stats</router-link>

            <hr class="border-gray-200 dark:border-gray-700" />

            <router-link to="/settings" class="dropdown-item">Settings</router-link>
            <router-link to="/publications" class="dropdown-item">Manage publications</router-link>

            <hr class="border-gray-200 dark:border-gray-700" />

            <button @click="logout" class="dropdown-item text-red-600 hover:text-red-700">
                Sign out
            </button>

            <div class="px-4 pb-3 pt-1 text-xs text-gray-400">{{ user.email }}</div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import UserAvatar from "@/pages/auth/UserAvatar.vue";

const auth = useAuthStore()
const user = computed(() => auth.user?.data ?? {})

const open = ref(false)

const logout = () => {
    auth.logout()
    open.value = false
}

</script>
