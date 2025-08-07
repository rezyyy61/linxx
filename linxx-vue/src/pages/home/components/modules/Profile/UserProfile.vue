<template>
  <!-- Loading State -->
  <div
      v-if="store.loading"
      class="flex items-center justify-center py-10 text-sm text-gray-500 dark:text-gray-400"
      dir="ltr"
  >
    {{ $t('politicalProfile.list.loading') }}
  </div>

  <!-- Loaded State -->
  <div v-else-if="store.profile" class="space-y-6 w-full" ref="scrollContainer">
    <!-- Header -->
    <UserHeader :user="store.profile" />

    <!-- Menu -->
    <UserProfileMenu v-model:active-tab="activeTab" />

    <!-- Two Column Layout -->
    <div v-if="activeTab === 'posts'" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Main Column -->
      <div class="lg:col-span-7 space-y-4">
        <ProfileMainColumn :profile="store.profile" :posts="store.posts" />

        <div v-if="store.postsLoading" class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">
          {{ $t('loading') }}...
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-5">
        <ProfileSidebar :user="store.profile" />
      </div>
    </div>

    <!-- Single Column Layout for Other Tabs -->
    <div v-else class="space-y-4">
      <ProfileAboutTab v-if="activeTab === 'about'" :profile="store.profile" />
      <ProfileVideosTab v-else-if="activeTab === 'videos'" :profile="store.profile" />
      <ProfilePhotosTab v-else-if="activeTab === 'photos'" :profile="store.profile" />
      <ProfileFilesTab v-else-if="activeTab === 'files'" :profile="store.profile" />
      <ProfileContactTab v-else-if="activeTab === 'contact'" :profile="store.profile" />
    </div>

  </div>

  <!-- Error State -->
  <div
      v-else
      class="flex items-center justify-center py-10 text-sm text-red-500 dark:text-red-400"
  >
    {{ $t('politicalProfile.list.error') }}
  </div>
</template>

<script setup>
import { watch, onMounted, onBeforeUnmount, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import UserHeader from '@/pages/home/components/modules/Profile/UserHeader.vue'
import UserProfileMenu from '@/pages/home/components/modules/Profile/UserProfileMenu.vue'
import ProfileMainColumn from '@/pages/home/components/modules/Profile/ProfileMainColumn.vue'
import ProfileSidebar from '@/pages/home/components/modules/Profile/ProfileSidebar.vue'
import ProfileAboutTab from '@/pages/home/components/modules/Profile/tabs/ProfileAboutTab.vue'
import ProfileVideosTab from '@/pages/home/components/modules/Profile/tabs/ProfileVideosTab.vue'
import ProfilePhotosTab from '@/pages/home/components/modules/Profile/tabs/ProfilePhotosTab.vue'
import ProfileFilesTab from '@/pages/home/components/modules/Profile/tabs/ProfileFilesTab.vue'
import ProfileContactTab from '@/pages/home/components/modules/Profile/tabs/ProfileContactTab.vue'
import { usePublicProfileStore } from '@/stores/politicalProfile/publicProfile'

const route = useRoute()
const store = usePublicProfileStore()
const scrollContainer = ref(null)
const activeTab = ref('posts')

const emit = defineEmits(['update:activeTab'])


const loadProfile = async (slug) => {
  store.resetProfile()
  store.resetProfilePosts()
  await store.fetchPublicProfile(slug)
  store.fetchProfilePosts(slug)
}

const handleScroll = async () => {
  if (store.postsLoading || store.allLoaded) return

  const threshold = 300
  const scrollPos = window.innerHeight + window.scrollY
  const totalHeight = document.body.offsetHeight

  if (scrollPos + threshold >= totalHeight) {
    await store.fetchProfilePosts(route.params.slug)
  }
}

onMounted(async () => {
  await loadProfile(route.params.slug)
  window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll)
})

watch(
    () => route.params.slug,
    async (newSlug) => {
      if (newSlug) {
        await loadProfile(newSlug)
      }
    }
)
</script>
