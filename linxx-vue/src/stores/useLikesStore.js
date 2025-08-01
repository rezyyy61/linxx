import { defineStore } from 'pinia'
import axios from '@/lib/axios'
import publicAxios from '@/lib/publicAxios'
import { useAuthStore } from '@/stores/auth'

export const useLikesStore = defineStore('likes', {
    state: () => ({
        likesMap: {},
        listenedPosts: []
    }),

    actions: {
        setInitial(postId, isLiked, count, preview = []) {
            this.likesMap[postId] = {
                isLiked,
                count,
                preview
            }

            this.listen(postId)
        },

        async toggle(postId) {
            const prev = this.likesMap[postId]

            const optimisticLiked = !prev.isLiked
            const optimisticCount = optimisticLiked ? prev.count + 1 : prev.count - 1
            this.likesMap[postId] = {
                ...prev,
                isLiked: optimisticLiked,
                count: optimisticCount
            }

            try {
                await axios.post(`/api/posts/${postId}/like`)
                const previewRes = await publicAxios.get(`/api/posts/${postId}/likes-preview`)

                this.likesMap[postId].preview = previewRes.data.users
            } catch (err) {
                console.error('❌ toggleLike failed:', err)
                this.likesMap[postId] = prev
            }
        },

        listen(postId) {
            if (this.listenedPosts.includes(postId)) return
            this.listenedPosts.push(postId)

            if (!window.Echo) return

            window.Echo.channel(`post.${postId}`)
                .listen('App\\Events\\PostLiked', async (data) => {
                    const authStore = useAuthStore()
                    const authUserId = authStore.user?.data.id

                    if (!this.likesMap[postId]) {
                        this.likesMap[postId] = { isLiked: false, count: 0, preview: [] }
                    }

                    this.likesMap[postId].count = data.likesCount
                    try {
                        const res = await publicAxios.get(`/api/posts/${postId}/likes-preview`)
                        this.likesMap[postId].preview = res.data.users
                    } catch (err) {
                        console.error('❌ Failed to fetch preview on realtime event', err)
                    }
                })
        }
    },

    getters: {
        isLiked: (state) => (postId) => state.likesMap[postId]?.isLiked ?? false,
        likesCount: (state) => (postId) => state.likesMap[postId]?.count ?? 0,
        previewNames: (state) => (postId) => state.likesMap[postId]?.preview ?? []
    }
})
