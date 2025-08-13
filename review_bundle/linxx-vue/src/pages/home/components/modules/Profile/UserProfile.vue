<template>
  <div class="w-full">
    <div v-if="store.loading" class="space-y-4">
      <div class="h-32 rounded-2xl bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
      <div class="h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
      <div class="grid gap-4 md:grid-cols-2">
        <div class="h-40 rounded-2xl bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
        <div class="h-40 rounded-2xl bg-zinc-100 dark:bg-zinc-800 animate-pulse" />
      </div>
    </div>

    <div v-else-if="store.profile" class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
      <UserHeader :user="store.profile" class="max-w-7xl"/>

      <UserProfileMenu v-model:active-tab="activeTab" class="max-w-6xl mx-auto"/>

      <div v-if="activeTab === 'posts'" class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 space-y-4">
          <ProfileMainColumn :profile="store.profile" :posts="store.posts" />
          <div v-if="store.postsLoading" class="flex items-center justify-center py-6 text-sm text-zinc-500 dark:text-zinc-400">
            {{ $t('loading') }}…
          </div>
          <div ref="sentinel" class="h-1"></div>
          <div v-if="store.allLoaded && !store.postsLoading" class="text-center py-4 text-xs text-zinc-400">
            {{ $t('noMore', 'No more items') }}
          </div>
        </div>

        <div class="lg:col-span-5">
          <ProfileSidebar :user="store.profile" />
        </div>
      </div>

      <KeepAlive>
        <component
            v-if="activeTab !== 'posts'"
            :is="activeTabComp"
            :profile="store.profile"
        />
      </KeepAlive>
    </div>

    <div v-else class="flex flex-col items-center justify-center gap-3 py-12">
      <div class="text-sm text-red-500 dark:text-red-400">
        {{ $t('politicalProfile.list.error') }}
      </div>
      <button
          @click="reload"
          class="px-3 py-1.5 text-sm rounded-lg ring-1 ring-inset ring-zinc-200 hover:bg-zinc-50 dark:ring-zinc-700 dark:hover:bg-zinc-800"
      >
        {{ $t('retry', 'Retry') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
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
const router = useRouter()
const store = usePublicProfileStore()

const allowedTabs = ['posts', 'about', 'videos', 'photos', 'files', 'contact']
const qTab = typeof route.query.tab === 'string' ? route.query.tab : ''
const activeTab = ref(allowedTabs.includes(qTab) ? qTab : 'posts')

const tabComponents = {
  about: ProfileAboutTab,
  videos: ProfileVideosTab,
  photos: ProfilePhotosTab,
  files: ProfileFilesTab,
  contact: ProfileContactTab
}
const activeTabComp = computed(() => tabComponents[activeTab.value] || null)

const sentinel = ref(null)
let observer = null

async function loadProfile(slug) {
  store.resetProfile()
  store.resetProfilePosts()
  await store.fetchPublicProfile(slug)
  store.fetchProfilePosts(slug)
  await nextTick()
  setupObserver()
}

function setupObserver() {
  if (observer) observer.disconnect()
  if (!sentinel.value) return
  observer = new IntersectionObserver(async (entries) => {
    const e = entries[0]
    if (!e || !e.isIntersecting) return
    if (activeTab.value !== 'posts') return
    if (store.postsLoading || store.allLoaded) return
    await store.fetchProfilePosts(route.params.slug)
  }, { root: null, rootMargin: '600px 0px', threshold: 0 })
  observer.observe(sentinel.value)
}

function destroyObserver() {
  if (observer) observer.disconnect()
  observer = null
}

async function reload() {
  const slug = route.params.slug
  if (!slug) return
  await loadProfile(slug)
}

onMounted(async () => {
  await loadProfile(route.params.slug)
})

onBeforeUnmount(() => {
  destroyObserver()
})

watch(() => route.params.slug, async (slug) => {
  if (slug) {
    destroyObserver()
    window.scrollTo({ top: 0, behavior: 'instant' })
    await loadProfile(slug)
  }
})

watch(activeTab, (tab) => {
  const q = { ...route.query, tab }
  router.replace({ query: q })
  if (tab === 'posts') setupObserver()
  else destroyObserver()
})

watch(() => route.query.tab, (nt) => {
  if (typeof nt === 'string' && allowedTabs.includes(nt) && nt !== activeTab.value) {
    activeTab.value = nt
  }
})
</script>
