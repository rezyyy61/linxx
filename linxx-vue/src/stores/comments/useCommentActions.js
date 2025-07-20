import { ref } from 'vue'
import { CommentAPI } from '@/stores/comments'

export function useCommentActions(comments = ref([]), emit = () => {}) {
    const editingId = ref(null)
    const editText = ref('')
    const saving = ref(false)
    const error = ref(null)

    // Track reply input states
    const expandedReplies = ref({})
    const collapsedComments = ref({})

    // === Editing ===
    function startEdit(comment) {
        editingId.value = comment.id
        editText.value = comment.text || comment.content || ''
    }

    function cancelEdit() {
        editingId.value = null
        editText.value = ''
    }

    async function saveEdit(comment) {
        const newText = editText.value.trim()
        const original = comment.text || comment.content
        if (!newText || newText === original) return

        saving.value = true
        error.value = null

        try {
            await CommentAPI.update(comment.id, newText)
            comment.text = newText // locally update comment
            editingId.value = null
            editText.value = ''
            // emit('refresh') is optional if real-time update is enabled
        } catch (e) {
            error.value = e
            console.error('Failed to save edit', e)
        } finally {
            saving.value = false
        }
    }

    // === Reply input ===
    function expandReply(commentId) {
        expandedReplies.value[commentId] = true
    }

    function collapseReply(commentId) {
        expandedReplies.value[commentId] = false
    }

    function clearReply(commentId) {
        expandedReplies.value[commentId] = false
    }

    function isReplyOpen(commentId) {
        return expandedReplies.value[commentId] === true
    }

    // === Collapse/Expand replies ===
    function toggleCollapse(commentId) {
        collapsedComments.value[commentId] = !collapsedComments.value[commentId]
    }

    function isCollapsed(commentId) {
        return collapsedComments.value[commentId] === true
    }

    return {
        editingId,
        editText,
        saving,
        error,
        startEdit,
        cancelEdit,
        saveEdit,
        expandedReplies,
        expandReply,
        collapseReply,
        clearReply,
        isReplyOpen,
        collapsedComments,
        toggleCollapse,
        isCollapsed
    }
}
