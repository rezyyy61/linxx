<template>
  <div class="max-w-3xl mx-auto w-full">
    <div v-if="loading" class="p-4 space-y-4">
      <div class="h-5 w-40 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
      <div class="h-40 w-full bg-gray-200 dark:bg-gray-700 rounded-xl animate-pulse"></div>
    </div>

    <div v-else-if="error" class="p-6 text-center text-red-600 dark:text-red-400">
      {{ error }}
    </div>

    <PostCard v-else-if="post" :post="post" />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostStore } from '@/stores/post'
import PostCard from '@/pages/home/components/modules/posts/PostCard.vue'

const route = useRoute()
const router = useRouter()
const postStore = usePostStore()

const loading = ref(true)
const error = ref('')
const post = ref(null)

async function load(id) {
  loading.value = true
  error.value = ''
  post.value = null
  try {
    const p = await postStore.fetchPostById(id)
    post.value = p
    postStore.upsertOne(p)
    const currentSlug = route.params.slug || ''
    const targetSlug = p.slug ? String(p.slug) : ''
    if (currentSlug !== targetSlug) {
      router.replace({ name: 'post.show', params: { id: p.id, slug: targetSlug || undefined } })
    }
  } catch (e) {
    error.value = 'پست پیدا نشد'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const id = Number(route.params.id || 0)
  if (id) load(id)
  else error.value = 'شناسه پست نامعتبر است'
})

watch(() => route.params.id, (v) => {
  const id = Number(v || 0)
  if (id) load(id)
})
</script>
