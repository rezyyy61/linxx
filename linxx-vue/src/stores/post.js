import { defineStore } from 'pinia'
import axios from '@/lib/axios'
import { ref } from 'vue'
import { htmlToRichJson } from '@/utils/htmlToRichJson'
import { useAuthStore } from '@/stores/auth'

function normalizeUser(u) {
    if (!u) return null
    const slug = u.slug || u.username || (u.id != null ? String(u.id) : undefined)
    return { ...u, slug }
}

function normalizePostShallow(raw) {
    if (!raw) return raw
    const slug = raw.slug || (raw.id != null ? String(raw.id) : undefined)
    const user = normalizeUser(raw.user)
    return {
        ...raw,
        slug,
        user,
        richText: Array.isArray(raw.richText) ? raw.richText : htmlToRichJson(raw.text || '')
    }
}

function normalizePost(raw) {
    if (!raw) return raw
    const base = normalizePostShallow(raw)
    if (raw.share) {
        base.share = {
            ...raw.share,
            shareable: raw.share.shareable
                ? normalizePostShallow({ ...raw.share.shareable, share: undefined })
                : null
        }
    }
    return base
}

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

    async function submitReshare({ shareSlug, text, original }) {
        try {
            const { data } = await axios.post('/api/reshare', { share_slug: shareSlug, text })
            const me = useAuthStore().user || {}

            const optimistic = normalizePost({
                ...data,
                text: text || data.text || '',
                user: {
                    id: me.id,
                    name: me.name,
                    avatar: me.avatar,
                    slug: me.slug || me.username || (me.id != null ? String(me.id) : undefined)
                },
                likes_count: 0,
                is_liked: false,
                likes_preview: [],
                comments_count: 0,
                media: Array.isArray(data.media) ? data.media : [],
                share: {
                    id: data.share_id || null,
                    slug: shareSlug,
                    shareable: original && (original.text != null || original.media != null)
                        ? normalizePost(original)
                        : (original || null)
                }
            })

            const idx = posts.value.findIndex(x => x.id === optimistic.id)
            if (idx === -1) posts.value.unshift(optimistic)
            else posts.value[idx] = optimistic

            return optimistic
        } catch (err) {
            console.error('Submit reshare error:', err)
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
            const resp = await axios.get(`/api/posts?page=${currentPage.value}`)
            const { data, meta } = resp.data
            posts.value.push(...data.map(normalizePost))
            currentPage.value++
            lastPage.value = meta.last_page
            if (currentPage.value > lastPage.value) allLoaded.value = true
        } catch (err) {
            console.error('Error fetching posts:', err)
            error.value = 'Failed to fetch posts'
        } finally {
            loading.value = false
        }
    }

    async function fetchPostById(id) {
        const resp = await axios.get(`/api/posts/${id}`)
        const raw = resp.data?.data ?? resp.data
        return normalizePost(raw)
    }

    function upsertOne(post) {
        const idx = posts.value.findIndex(x => x.id === post.id)
        if (idx === -1) posts.value.unshift(post)
        else posts.value[idx] = post
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
                const post = normalizePost(p.post)
                const idx = posts.value.findIndex(x => x.id === post.id)
                if (idx !== -1) posts.value[idx] = post
                else posts.value.unshift(post)
            })
    }

    function subscribeUserChannel(userId) {
        if (!userId) return
        window.Echo.private(`user.${userId}`)
            .listen('PostQueued', p => {
                const stub = normalizePost({ ...p.post, media: [], created_at: null })
                if (!posts.value.find(x => x.id === stub.id)) {
                    posts.value.unshift({ ...stub, _localPending: true })
                }
            })
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
        submitReshare,
        fetchMorePosts,
        resetPagination,
        reset,
        subscribeRealtime,
        subscribeUserChannel,
        fetchPostById,
        upsertOne
    }
})
