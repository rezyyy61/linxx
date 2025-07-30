import { defineStore } from 'pinia'
import axios from '@/lib/axios'
import { ref } from 'vue'
import { htmlToRichJson } from '@/utils/htmlToRichJson'

export const usePostStore = defineStore('post', () => {
    const postText = ref('')
    const visibility = ref('public')
    const isArchived = ref(false)
    const images = ref([])
    const videos = ref([])
    const audio = ref(null)
    const files = ref([])

    const posts = ref([])
    const currentPage = ref(1)
    const lastPage = ref(null)
    const loading = ref(false)
    const allLoaded = ref(false)
    const error = ref(null)

    async function submitPost() {
        try {
            const formData = new FormData()
            formData.append('text', postText.value)
            formData.append('visibility', visibility.value || 'public')
            formData.append('is_archived', isArchived.value ? '1' : '0')

            images.value.forEach((item, i) => formData.append(`images[${i}]`, item.file))
            if (videos.value.length) formData.append('video', videos.value[0].file)
            if (audio.value) formData.append('audio', audio.value.file)
            files.value.forEach((file, i) => formData.append(`files[${i}]`, file))

            const { data } = await axios.post('/api/posts', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })

            reset()
            return data
        } catch (err) {
            console.error('Submit post error:', err)
            throw err
        }
    }

    function reset() {
        postText.value = ''
        images.value = []
        videos.value = []
        audio.value = null
        files.value = []
    }

    async function fetchMorePosts() {
        if (loading.value || allLoaded.value) return

        loading.value = true
        error.value = null

        try {
            const { data, meta } = (await axios.get(`/api/posts?page=${currentPage.value}`)).data
            posts.value.push(...data.map(normalizePost))
            currentPage.value++
            lastPage.value = meta.last_page
            if (currentPage.value > lastPage.value) allLoaded.value = true
        } catch (err) {
            console.error('Error fetching posts:', err)
            error.value = 'خطا در دریافت پست‌ها'
        } finally {
            loading.value = false
        }
    }

    function resetPagination() {
        posts.value = []
        currentPage.value = 1
        lastPage.value = null
        allLoaded.value = false
        error.value = null
    }

    function subscribeRealtime() {
        window.Echo.channel('public-feed')
            .listen('PostCreated', p => posts.value.unshift(normalizePost(p.post)))
            .listen('PostReady', p => {
                const idx = posts.value.findIndex(x => x.id === p.post.id)
                if (idx !== -1) posts.value[idx] = normalizePost(p.post)
                else posts.value.unshift(normalizePost(p.post))
            })
    }

    function subscribeUserChannel(userId) {
        if (!userId) return
        window.Echo.private(`user.${userId}`)
            .listen('PostQueued', p => {
                if (!posts.value.find(x => x.id === p.post.id)) {
                    posts.value.unshift({
                        ...normalizePost(p.post),
                        media: [],
                        _localPending: true,
                        created_at: null
                    })
                }
            })
    }

    function normalizePost(raw) {
        return {
            ...raw,
            richText: Array.isArray(raw.richText) ? raw.richText : htmlToRichJson(raw.text || '')
        }
    }

    return {
        postText,
        images,
        videos,
        audio,
        files,
        visibility,
        isArchived,
        posts,
        currentPage,
        lastPage,
        loading,
        allLoaded,
        error,
        submitPost,
        fetchMorePosts,
        resetPagination,
        reset,
        subscribeRealtime,
        subscribeUserChannel
    }
})
