<!-- Valed.vue -->
<template>
  <section class="py-10 min-h-screen text-zinc-900 dark:text-zinc-100">
    <div class="w-[80%] mx-auto space-y-12">

      <div v-for="(group, index) in groupedVideos" :key="'group-' + index" class="space-y-10">

        <!-- Landscape Videos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
          <VideoCard
              v-for="video in group.landscape"
              :key="'landscape-' + video.id"
              :video="video"
              size="lg"
              orientation="landscape"
          />
        </div>

        <!-- Portrait Videos -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-1 sm:gap-2">
          <VideoCard
              v-for="video in group.portrait"
              :key="'portrait-' + video.id"
              :video="video"
              size="sm"
              orientation="portrait"
          />
        </div>

      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useVideoStore } from '@/stores/useVideoStore'
import VideoCard from "@/pages/home/components/modules/VideoCard/VideoCard.vue";

const store = useVideoStore()

onMounted(() => {
    if (!store.videos.length) store.fetchVideos()
})

const landscapeVideos = computed(() =>
    store.videos.filter(v => {
        const m = v.media?.[0]?.meta
        return m?.original_width / m?.original_height >= 1
    })
)

const portraitVideos = computed(() =>
    store.videos.filter(v => {
        const m = v.media?.[0]?.meta
        return m?.original_width / m?.original_height < 1
    })
)


const groupedVideos = computed(() => {
    const groups = []
    const landscape = [...landscapeVideos.value]
    const portrait = [...portraitVideos.value]

    while (landscape.length || portrait.length) {
        groups.push({
            landscape: landscape.splice(0, 6),
            portrait: portrait.splice(0, 5),
        })
    }

    return groups
})
</script>
