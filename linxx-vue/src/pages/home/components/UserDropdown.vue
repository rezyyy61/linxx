<template>
    <div class="relative" v-click-outside="() => open = false">
        <!-- Avatar Button -->
        <button
            @click="open = !open"
            class="w-9 h-9 rounded-full bg-red-600 text-white uppercase flex items-center justify-center font-bold hover:bg-red-700 transition"
        >
            {{ user.name.charAt(0) }}
        </button>

        <!-- Dropdown Menu -->
        <div
            v-if="open"
            class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-xl z-50 overflow-hidden animate-fade"
        >
            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">👤 {{ user.name }}</div>
            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Main Links -->
            <router-link to="/profile" class="dropdown-item">Profile</router-link>
            <router-link to="/library" class="dropdown-item">Library</router-link>
            <router-link to="/stories" class="dropdown-item">Stories</router-link>
            <router-link to="/stats" class="dropdown-item">Stats</router-link>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Settings -->
            <router-link to="/settings" class="dropdown-item">Settings</router-link>
            <router-link to="/publications" class="dropdown-item">Manage publications</router-link>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Logout -->
            <button @click="logout" class="dropdown-item text-red-600 hover:text-red-700">
                Sign out
            </button>

            <div class="px-4 pb-3 pt-1 text-xs text-gray-400">{{ user.email }}</div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const user = auth.user
const open = ref(false)

const logout = () => {
    auth.logout()
    open.value = false
}
</script>

<style scoped>
.dropdown-item {
    @apply block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition;
}
@keyframes fade {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade {
    animation: fade 0.15s ease-out;
}
</style>
