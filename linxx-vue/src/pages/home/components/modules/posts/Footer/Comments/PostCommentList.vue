<template>
    <div class="space-y-4 font-sans text-[15px] leading-relaxed max-h-[500px]">
        <div v-for="comment in mainComments" :key="comment.id">
            <!-- Main comment -->
            <CommentItem
                :comment="comment"
                :depth="0"
                :replies="repliesFor(comment.id)"
                :show-replies="showReplies[comment.id]"
                :has-replies="repliesFor(comment.id).length > 0"
                @toggle-replies="toggleReplies"
                @like="$emit('like', $event)"
                @reply="$emit('reply', $event)"
                @edit="handleEdit"
                @delete="handleDelete"
            />

            <!-- Replies to main comment -->
            <template v-if="showReplies[comment.id]">
                <div
                    v-for="reply in repliesFor(comment.id)"
                    :key="reply.id"
                    class="mt-3 ml-8 pl-8"
                >
                    <CommentItem
                        :comment="reply"
                        :depth="1"
                        :is-reply="true"
                        @like="$emit('like', $event)"
                        @reply="$emit('reply', $event)"
                        @edit="handleEdit"
                        @delete="handleDelete"
                    />
                </div>
            </template>
        </div>

        <p v-if="!comments.length" class="text-sm text-gray-400 dark:text-gray-500">
            {{ $t('post.noComments') || 'No comments yet.' }}
        </p>

        <div v-if="bottomMarker" ref="bottomMarker" :style="{ height: bottomSpacer + 'px' }" />
    </div>
</template>

<script setup>
import { computed, reactive } from 'vue'
import CommentItem from './CommentItem.vue'

const props = defineProps({
    comments: { type: Array, default: () => [] },
    bottomSpacer: { type: Number, default: 24 },
    bottomMarker: Boolean
})

const emit = defineEmits(['like', 'reply', 'edit', 'delete'])

const showReplies = reactive({})

const mainComments = computed(() =>
    props.comments.filter(c => c.parent_id === null)
)

const replyMap = computed(() => {
    const map = {}
    for (const c of props.comments) {
        if (c.parent_id) {
            if (!map[c.parent_id]) map[c.parent_id] = []
            map[c.parent_id].push(c)
        }
    }
    return map
})

const repliesFor = (parentId) => replyMap.value[parentId] || []

function toggleReplies(commentId) {
    showReplies[commentId] = !(showReplies[commentId] ?? false)
}

function handleEdit(comment) {
    emit('edit', comment)
}

function handleDelete(comment) {
    if (confirm('Are you sure you want to delete this comment?')) {
        emit('delete', comment.id)
    }
}
</script>
