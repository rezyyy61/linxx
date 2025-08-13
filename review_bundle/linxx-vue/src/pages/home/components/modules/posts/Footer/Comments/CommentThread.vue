<template>
    <div class="flex flex-col h-full max-h-[70vh]">

        <!-- Comment list - scrollable -->
        <div ref="scrollContainer" class="flex-1 overflow-y-auto px-4 pt-4 space-y-6 custom-scroll">
            <PostCommentList
                :comments="comments"
                @like="toggleLike"
                @reply="startReply"
                @edit="startEdit"
                @delete="remove"
            />
        </div>

        <!-- Fixed bottom input -->
        <div class="border-t dark:border-gray-700 mt-4">
            <PostCommentInput
                v-model="inputVal"
                :replying-to="replyingTo"
                :is-editing="isEditing"
                @send="handleSend"
                @cancel-reply="clearReply"
                @cancel-edit="clearEdit"
                :errors="errors"
                :get-error="getError"
            />

        </div>

    </div>
</template>


<script setup>
import {nextTick, ref} from 'vue'
import { useComments, mapComment } from '@/stores/comments/useComments'
import PostCommentInput from "@/pages/home/components/modules/posts/Footer/Comments/PostCommentInput.vue";
import PostCommentList from "@/pages/home/components/modules/posts/Footer/Comments/PostCommentList.vue";
import {useFormErrors} from "@/composables/useFormErrors";
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
    postId: {
        type: [String, Number],
        required: true
    }
})

const auth = useAuthStore()
const scrollContainer = ref(null)
const replyingTo = ref(null)
const editingComment = ref(null)
const isEditing = ref(false)

const {
    comments,
    inputVal,
    send,
    update,
    remove,
    toggleLike,
    load,
    totalCount,
    onNewComment,
} = useComments(props.postId)

const { errors, setErrorsFromResponse, getError, clearErrors } = useFormErrors()

async function handleSend({ content, parentId }) {
    const trimmed = content?.trim()
    if (!trimmed) return

    // Edit mode
    if (isEditing.value && editingComment.value) {
        try {
            await update(editingComment.value.id, trimmed)
            clearEdit()
            clearErrors()
        } catch (error) {
            setErrorsFromResponse(error)
        }
        return
    }

    // Temporary comment
    const tempId = `temp-${Date.now()}`
    const fakeComment = {
        id: tempId,
        post_id: props.postId,
        parent_id: parentId,
        post_user_id: null,
        name: auth.user.data?.name || 'You',
        avatar: auth.user.data?.avatar || null,
        avatarColor: auth.user.data?.avatar_color || '#999',
        user: auth.user.data,
        text: trimmed,
        likes: 0,
        liked: false,
        replies_count: 0,
        created_at: new Date().toISOString(),
        isTemp: true,
    }

    comments.value.push(fakeComment)
    inputVal.value = ''
    clearReply()
    scrollToBottom()

    try {
        await send({ content: trimmed, parentId })
        const idx = comments.value.findIndex(c => c.id === tempId)
        clearErrors()
    } catch (error) {
        comments.value = comments.value.filter(c => c.id !== tempId)
        setErrorsFromResponse(error)
    }
}

function startReply(comment) {
    replyingTo.value = comment
    isEditing.value = false
}

function clearReply() {
    replyingTo.value = null
}

function startEdit(comment) {
    inputVal.value = comment.text || comment.content || ''
    editingComment.value = comment
    isEditing.value = true
    replyingTo.value = null
}

function clearEdit() {
    editingComment.value = null
    isEditing.value = false
    inputVal.value = ''
}

function scrollToBottom() {
    nextTick(() => {
        if (scrollContainer.value) {
            const container = scrollContainer.value
            const shouldScroll = container.scrollHeight - container.scrollTop - container.clientHeight > 100

            if (shouldScroll || container.scrollTop < container.scrollHeight - 100) {
                container.scrollTo({
                    top: container.scrollHeight + 160,
                    behavior: 'smooth'
                })
            }
        }
    })
}
load()

onNewComment(() => {
    nextTick(() => {
        requestAnimationFrame(() => {
            scrollToBottom()
        })
    })
})

</script>
