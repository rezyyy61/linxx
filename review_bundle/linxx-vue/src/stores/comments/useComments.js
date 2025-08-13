// src/stores/comments/useComments.js

import {ref, onMounted, nextTick} from 'vue'
import { useAuthStore } from '@/stores/auth'
import {CommentAPI} from "@/stores/comments";


let newCommentCallback = () => {}
export function useComments(postId, options = {}) {
    const endOfComments = options.endOfComments || null
    const comments = ref([])
    const loading = ref(false)
    const inputVal = ref('')
    const totalCount = ref(0)
    const auth = useAuthStore()


    function onNewComment(cb) {
        newCommentCallback = cb || (() => {})
    }

    function mapComment(raw) {
        if (!raw) return null
        return {
            id: raw.id,
            post_id: raw.post_id,
            post_user_id: raw.post_user_id,
            parent_id: raw.parent_id,
            name: raw.user?.name || 'Anonymous',
            avatar: raw.user?.avatar || null,
            avatarColor: raw.user?.avatar_color || '#b80425',
            user: raw.user,
            text: raw.content,
            likes: raw.like_count || 0,
            liked: !!raw.liked_by_user,
            replies_count: raw.replies_count || 0,
            created_at: raw.created_at,
        }
    }

    async function load() {
        try {
            loading.value = true
            const token = auth.token
            const response = await CommentAPI.list(postId, token)
            const raw = response.data?.data || []
            comments.value = raw.map(mapComment)
            totalCount.value = comments.value.length
        } catch (e) {
            console.error('Failed to load comments', e)
        } finally {
            loading.value = false
        }
    }

    function listenRealtime() {
        window.Echo.channel(`post.${postId}`)
            .listen('.comment.created', (e) => {
                if (!e?.comment) return

                const real = mapComment(e.comment)
                if (!real) return

                // اگر یک fake با همین متن داریم، جایگزین کن
                const fakeIndex = comments.value.findIndex(c =>
                    c.isTemp && c.text === real.text && c.user?.id === real.user?.id
                )

                if (fakeIndex !== -1) {
                    comments.value[fakeIndex] = real
                } else {
                    comments.value.push(real)
                }

                totalCount.value = e.total_comments ?? totalCount.value + 1
                nextTick(() => requestAnimationFrame(() => newCommentCallback()))
            })

            .listen('.comment.updated', (e) => {
                const updated = mapComment(e.comment)
                const idx = comments.value.findIndex(c => c.id === updated.id)
                if (idx !== -1) comments.value[idx] = updated
            })

            .listen('.comment.deleted', (e) => {
                const id = e.commentId ?? e.comment_id ?? e.id
                comments.value = comments.value.filter(c => c.id !== id)
                totalCount.value = Math.max(0, totalCount.value - 1)
            })
    }

    async function send({ content, parentId = null }) {
        if (!auth.user.data.id || !auth.token) return

        const cleanContent = content?.trim()
        if (!cleanContent) return

        try {
            return await CommentAPI.create({
                post_id: postId,
                content: cleanContent,
                parent_id: parentId
            })
        } catch (e) {
            console.error('Failed to send comment', e)
            throw e
        }
    }


    async function update(commentId, newContent) {
        const content = newContent?.trim()
        if (!content) return

        try {
            const res = await CommentAPI.update(commentId, content)
            const updated = mapComment(res.data?.data)
            const index = comments.value.findIndex(c => c.id === commentId)

            if (index !== -1 && updated) {
                const prev = comments.value[index]
                comments.value[index] = {
                    ...prev,
                    ...updated,
                    user: prev.user,
                    post_user_id: prev.post_user_id,
                    comment_user_id: prev.comment_user_id ?? prev.user?.id
                }
            }
        } catch (e) {
            console.error('Failed to update comment', e)
        }
    }


    async function remove(commentId) {
        try {
            await CommentAPI.delete(commentId)
            const deleted = comments.value.find(c => c.id === commentId)
            comments.value = comments.value.filter(c => c.id !== commentId)
            if (deleted?.parent_id) {
                const parent = comments.value.find(c => c.id === deleted.parent_id)
                if (parent) parent.replies_count = Math.max(0, parent.replies_count - 1)
            }
            totalCount.value = Math.max(0, totalCount.value - 1)
        } catch (e) {
            console.error('Failed to delete comment', e)
        }
    }

    async function toggleLike(comment) {
        const originalLiked = comment.liked
        const delta = originalLiked ? -1 : 1
        comment.liked = !originalLiked
        comment.likes += delta

        try {
            await CommentAPI.toggleLike(comment.id)
        } catch (e) {
            comment.liked = originalLiked
            comment.likes -= delta
            console.error('Failed to toggle like', e)
        }
    }

    function updateLike(id, count) {
        const c = comments.value.find(c => c.id === id)
        if (c) c.likes = count
    }

    onMounted(async () => {
        await load()
        listenRealtime()
    })

    return {
        comments,
        inputVal,
        loading,
        totalCount,
        load,
        send,
        update,
        remove,
        toggleLike,
        onNewComment,
    }
}
