<template>
  <div
      class="w-full mx-auto"
      :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
  >
    <div class="grid grid-cols-1
             lg:grid-cols-[260px_minmax(0,720px)_260px]
             xl:grid-cols-[300px_minmax(0,760px)_300px]
             2xl:grid-cols-[320px_minmax(0,820px)_320px]
             gap-4 lg:gap-8 px-4 md:px-6 max-w-[1400px] mx-auto">

      <!-- Left rail -->
      <LeftSidebarRail />

      <!-- Center: posts -->
      <main class="mx-auto w-full space-y-6">
        <div class="space-y-6 max-w-[700px] mx-auto">
          <div v-for="post in posts" :key="post.id">
            <PostCard :post="post" />
          </div>
        </div>
        <div v-if="loading" class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">
          {{ $t('post.loading') }}...
        </div>
        <div v-if="!loading && allLoaded" class="text-center text-xs text-gray-400 dark:text-gray-500 py-6">
          {{ $t('post.noMorePosts') }}
        </div>
        <div v-if="error" class="text-center text-red-500 dark:text-red-400 py-4">
          {{ $t('post.somethingWentWrong') }}
        </div>
        <div ref="sentinel" />
      </main>

      <!-- Right rail -->
      <RightSidebarRail />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { usePostStore } from '@/stores/post'
import { useAuthStore } from '@/stores/auth'
import PostCard from "@/pages/home/components/modules/posts/PostCard.vue"
import RightSidebarRail from "@/pages/home/components/modules/Feed/RightSidebarRail.vue";
import LeftSidebarRail from "@/pages/home/components/modules/Feed/LeftSidebarRail.vue";

const postStore = usePostStore()
const authStore = useAuthStore()

const posts = computed(() => postStore.posts)
const loading = computed(() => postStore.loading)
const allLoaded = computed(() => postStore.allLoaded)
const error = computed(() => postStore.error)

const sentinel = ref(null)
let observer
let unsubRealtime
let unsubUser

function loadMore() {
  if (!loading.value && !allLoaded.value) postStore.fetchMorePosts()
}

onMounted(async () => {
  postStore.resetPagination()
  await postStore.fetchMorePosts()
  observer = new IntersectionObserver(([e]) => {
    if (e.isIntersecting) loadMore()
  }, { rootMargin: '300px' })
  await nextTick()
  if (sentinel.value) observer.observe(sentinel.value)
  unsubRealtime = postStore.subscribeRealtime?.()
  if (authStore.user?.id) unsubUser = postStore.subscribeUserChannel?.(authStore.user.id)
})

onBeforeUnmount(() => {
  observer?.disconnect()
  unsubRealtime?.()
  unsubUser?.()
})
</script>
