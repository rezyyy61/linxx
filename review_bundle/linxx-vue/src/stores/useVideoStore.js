import { defineStore } from 'pinia'
import { ref } from 'vue'
import publicAxios from '@/lib/publicAxios'
import axios from '@/lib/axios'
import { htmlToRichJson } from '@/utils/htmlToRichJson'

export const useVideoStore = defineStore('video', () => {
    const videos = ref([])
    const loading = ref(false)
    const error = ref(null)
    const currentPage = ref(1)
    const lastPage = ref(null)
    const allLoaded = ref(false)
    const selectedVideo = ref(null)

    // ✅ Normalize video to ensure richText is available
    function normalizeVideo(video) {
        return {
            ...video,
            richText: Array.isArray(video.richText)
                ? video.richText
                : htmlToRichJson(video.text || '')
        }
    }

    async function fetchVideos() {
        if (loading.value || allLoaded.value) return

        loading.value = true
        error.value = null

        try {
            const response = await publicAxios.get(`/api/videos?page=${currentPage.value}`)
            const { data, meta } = response.data

            // 🛠 Normalize all videos
            videos.value.push(...data.map(normalizeVideo))

            currentPage.value++
            lastPage.value = meta.last_page

            if (currentPage.value > lastPage.value) {
                allLoaded.value = true
            }
        } catch (err) {
            console.error('Failed to fetch videos:', err)
            error.value = 'Error loading videos'
        } finally {
            loading.value = false
        }
    }

    async function fetchVideoById(id) {
        loading.value = true
        error.value = null
        selectedVideo.value = null

        try {
            const res = await axios.get(`/api/videos/${id}`)
            selectedVideo.value = normalizeVideo(res.data.data)
        } catch (err) {
            console.error('Failed to fetch video:', err)
            error.value = 'Error loading video'
        } finally {
            loading.value = false
        }
    }

    function getRelatedVideos(currentVideoId, count = 4) {
        return videos.value
            .filter(v => {
                const isLandscape = v.media?.[0]?.meta?.original_width > v.media?.[0]?.meta?.original_height
                return v.id !== currentVideoId && isLandscape
            })
            .slice(0, count)
    }

    function resetVideos() {
        videos.value = []
        currentPage.value = 1
        lastPage.value = null
        allLoaded.value = false
        error.value = null
    }

    return {
        videos,
        loading,
        error,
        currentPage,
        lastPage,
        allLoaded,
        selectedVideo,
        fetchVideos,
        fetchVideoById,
        resetVideos,
        getRelatedVideos
    }
})
