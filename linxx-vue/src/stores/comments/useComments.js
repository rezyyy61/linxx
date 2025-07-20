import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { CommentAPI } from "@/stores/comments"

export function useComments(postId) {
    const comments = ref([])
    const loading = ref(false)
    const inputVal = ref('')
    const totalCount = ref(0)
    const auth = useAuthStore()

    function mapComment(raw) {
        if (!raw) return null
        return {
            id: raw.id,
            parent_id: raw.parent_id,
            name: raw.user?.name || 'Anonymous',
            avatar: raw.user?.avatar || `https://i.pravatar.cc/40?u=${raw.user?.id || 'guest-' + raw.id}`,
            user: raw.user,
            text: raw.content,
            likes: raw.like_count || 0,
            liked: !!raw.liked_by_user,
            replies_count: raw.replies_count || 0,
            created_at: raw.created_at,
            children: (raw.children || []).map(mapComment).filter(Boolean)
        }
    }

    function countAll(commentsList) {
        return commentsList.reduce((acc, c) => acc + 1 + countAll(c.children || []), 0)
    }

    async function load() {
        try {
            loading.value = true
            const token = auth.token
            const response = await CommentAPI.list(postId, token)
            const raw = response.data?.data || []
            comments.value = raw.map(mapComment)
            totalCount.value = countAll(comments.value)
        } catch (e) {
            console.error('Failed to load comments', e)
        } finally {
            loading.value = false
        }
    }

    function listenRealtime() {
        window.Echo.channel(`post.${postId}`)
            .listen('.comment.created', (e) => {
                if (!e?.comment) {
                    console.warn('[Realtime] Payload invalid (missing comment):', e)
                    return
                }

                const newComment = mapComment(e.comment)

                if (!newComment) {
                    console.warn('[Realtime] Failed to map comment from payload:', e.comment)
                    return
                }

                insertComment(newComment)

                if (e.total_comments !== undefined) {
                    totalCount.value = e.total_comments
                } else {
                    totalCount.value += 1
                }
            })
            .listen('.comment.liked', (e) => {
                if (!e?.comment_id) {
                    console.warn('[Realtime] Invalid like event:', e)
                    return
                }
                updateLike(e.comment_id, e.like_count)
            })
    }

    function insertComment(newComment) {
        if (!newComment.parent_id) {
            comments.value = [...comments.value, newComment]
            return
        }

        const addReply = (list) => {
            for (const c of list) {
                if (c.id === newComment.parent_id) {
                    c.children.push(newComment)
                    return true
                } else if (c.children.length) {
                    if (addReply(c.children)) return true
                }
            }
            return false
        }

        addReply(comments.value)
    }

    function updateLike(id, likeCount) {
        const update = (list) => {
            for (const c of list) {
                if (c.id === id) {
                    c.likes = likeCount
                    return true
                } else if (c.children.length) {
                    if (update(c.children)) return true
                }
            }
            return false
        }
        update(comments.value)
    }

    async function send({ content, parentId = null }) {
        if (!auth.user?.id) return
        const cleanContent = content?.trim()
        if (!cleanContent) return

        try {
            await CommentAPI.create({
                post_id: postId,
                content: cleanContent,
                parent_id: parentId
            })
            inputVal.value = ''
        } catch (e) {
            console.error('Failed to send comment', e)
        }
    }

    async function update(commentId, content) {
        const cleanContent = content?.trim()
        if (!cleanContent) return
        try {
            await CommentAPI.update(commentId, cleanContent)
        } catch (e) {
            console.error('Failed to update comment', e)
        }
    }
    async function remove(commentId) {
        try {
            await CommentAPI.delete(commentId)
            const findAndDecrement = (list, parent = null) => {
                for (const c of list) {
                    if (c.id === commentId && parent) {
                        parent.replies_count = Math.max(0, (parent.replies_count || 1) - 1)
                        break
                    }
                    if (c.children?.length) {
                        findAndDecrement(c.children, c)
                    }
                }
            }
            findAndDecrement(comments.value)
            const deleteRecursive = (list) => {
                return list
                    .map(c => ({ ...c }))
                    .filter(c => {
                        if (c.id === commentId) return false
                        if (c.children?.length) {
                            c.children = deleteRecursive(c.children)
                        }
                        return true
                    })
            }

            comments.value = deleteRecursive(comments.value)
            totalCount.value = Math.max(0, totalCount.value - 1)
        } catch (e) {
            console.error('Failed to delete comment', e)
        }
    }

    async function toggleLike(comment) {
        const original = comment.liked
        const delta = original ? -1 : 1

        comment.liked = !original
        comment.likes += delta

        try {
            await CommentAPI.toggleLike(comment.id)
        } catch (e) {
            comment.liked = original
            comment.likes -= delta
            console.error('Failed to toggle like', e)
        }
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
        toggleLike
    }
}
