<template>
  <div class="relative">
    <aside
        v-if="isLoggedIn"
        class="fixed right-3 md:right-4 top-[58%] -translate-y-1/2 z-40 hidden md:flex flex-col items-center gap-2
             p-2 rounded-2xl bg-white/90 backdrop-blur shadow-lg border border-gray-200
             dark:bg-[#111319]/80 dark:border-white/10"
    >
      <button class="group relative" @click="goToProfile">
        <UserAvatar :src="avatarSrc" :fallback="userName" :color="avatarColor" size="md" />
        <span
            class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white dark:border-[#111319]"
            :class="user.online ? 'bg-green-500' : 'bg-gray-400'"
        />
      </button>

      <div class="scale-90 relative">
        <EdgeAction
            icon="mdi:bell-outline"
            :badge="notifBadge"
            label="Notifications"
            @click="toggleDropdown"
        />
        <NotificationsDropdown
            :open="dropdownOpen"
            :items="notif.items"
            :loading="notif.loading"
            @close="dropdownOpen = false"
            @mark-seen="notif.markAllSeen"
            @open-item="openItem"
            @view-all="goToNotifications"
        />
      </div>

      <div class="scale-90">
        <EdgeAction icon="mdi:pencil-plus" label="New Post" highlight @click="openCreatePostModal" />
      </div>

      <div class="h-px w-8 my-1 bg-gray-200 dark:bg-white/10" />

      <div class="scale-90">
        <EdgeAction :icon="isDark ? 'mdi:moon-waning-crescent' : 'mdi:white-balance-sunny'" :label="isDark ? 'Dark' : 'Light'" @click="$emit('toggle-theme')" />
      </div>
      <div class="scale-90">
        <EdgeAction icon="mdi:translate" :label="locale.toUpperCase()" @click="$emit('toggle-locale')" />
      </div>
      <div class="scale-90">
        <EdgeAction icon="mdi:view-dashboard-outline" label="Dashboard" @click="openDashboard" />
      </div>

      <div class="h-px w-8 my-1 bg-gray-200 dark:bg-white/10" />

      <div class="scale-90">
        <EdgeAction icon="mdi:arrow-up" label="Top" @click="scrollTop" />
      </div>
    </aside>

    <CreatePostModal v-if="showCreatePost" @close="closeCreatePostModal" />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import UserAvatar from '@/pages/auth/UserAvatar.vue'
import EdgeAction from '@/pages/home/components/edge/EdgeAction.vue'
import CreatePostModal from '@/pages/home/components/CreatePostModal.vue'
import {useNotificationsStore} from "@/stores/notifications/notifications";
import NotificationsDropdown from "@/pages/home/components/notifications/NotificationsDropdown.vue";

export default {
  name: 'EdgeUserDock',
  components: { EdgeAction, UserAvatar, CreatePostModal, NotificationsDropdown },
  props: {
    counts: { type: Object, default: () => ({ notifications: 0, messages: 0 }) },
    isDark: { type: Boolean, default: false },
    locale: { type: String, default: 'en' }
  },
  emits: ['open-notifications', 'open-messages', 'toggle-theme', 'toggle-locale'],
  setup () {
    const showCreatePost = ref(false)
    const dropdownOpen = ref(false)

    const authStore = useAuthStore()
    const notif = useNotificationsStore()

    onMounted(async () => {
      if (!authStore.user?.id && typeof authStore.fetchUser === 'function') {
        try { await authStore.fetchUser() } catch {}
      }
      if (authStore.user?.id) {
        await notif.fetchCounter()
        await notif.fetchLatest(1, 10)
        notif.connect(authStore.user.id)
      }
    })

    const user = computed(() => authStore.user || {})
    const isLoggedIn = computed(() => !!user.value?.id)
    const userName = computed(() => user.value.name || 'User')
    const avatarColor = computed(() => user.value.avatar_color || '#10B981')
    const avatarSrc = computed(() => user.value.logo_url || user.value.avatar || null)
    const notifBadge = computed(() => notif.badge)

    watch(() => user.value?.id, async (id, old) => {
      if (id && id !== old) {
        await notif.fetchCounter()
        await notif.fetchLatest(1, 10)
        notif.connect(id)
      }
      if (!id && old) notif.disconnect()
    })

    const openCreatePostModal = () => { showCreatePost.value = true }
    const closeCreatePostModal = () => { showCreatePost.value = false }
    const scrollTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }) }
    const openDashboard = () => { window.open('http://localhost:8081/dashboard', '_blank', 'noopener') }
    const goToProfile = () => {
      if (user.value?.slug) {
        window.location.href = `/${encodeURIComponent(user.value.slug)}`
      }
    }
    const goToNotifications = () => {
      window.location.href = '/notifications'
    }
    const toggleDropdown = async () => {
      dropdownOpen.value = !dropdownOpen.value
      if (dropdownOpen.value && notif.badge > 0) {
        try { await notif.markAllSeen() } catch {}
      }
    }
    const openItem = (n) => {
      if (n?.id) notif.markRead(n.id)
      if (n?.action_url) window.location.href = n.action_url
      dropdownOpen.value = false
    }

    return {
      user,
      isLoggedIn,
      userName,
      avatarColor,
      avatarSrc,
      notif,
      notifBadge,
      dropdownOpen,
      showCreatePost,
      openCreatePostModal,
      closeCreatePostModal,
      scrollTop,
      openDashboard,
      goToProfile,
      goToNotifications,
      toggleDropdown,
      openItem
    }
  }
}
</script>

<style scoped>
.slide-profile-enter-active,
.slide-profile-leave-active { transition: all .3s ease; }
.slide-profile-enter-from { opacity: 0; transform: translateX(20px) translateY(-50%); }
.slide-profile-leave-to { opacity: 0; transform: translateX(20px) translateY(-50%); }
</style>
