<template>
    <div class="space-y-6 font-sans text-[15px] leading-relaxed">
        <CommentItem
            v-for="(comment, index) in comments"
            :key="comment.id || index"
            :comment="comment"
            :editing-id="editingId"
            :edit-text="editText"
            :saving="saving"
            :error="error"
            :start-edit="startEdit"
            :cancel-edit="cancelEdit"
            :save-edit="saveEdit"
            @like="$emit('like', $event)"
            @reply="$emit('reply', $event)"
            @delete="$emit('delete', $event)"
            @refresh="$emit('refresh')"
        />

        <p v-if="!comments.length" class="text-sm text-gray-400 dark:text-gray-500">
            {{ $t('post.noComments') || 'No comments yet.' }}
        </p>

        <div v-if="bottomMarker" ref="bottomMarker" :style="{ height: bottomSpacer + 'px' }" />
    </div>
</template>

<script setup>
import CommentItem from './CommentItem.vue'
import { useCommentActions } from '@/stores/comments/useCommentActions'

const props = defineProps({
    comments: { type: Array, default: () => [] },
    bottomSpacer: { type: Number, default: 24 },
    bottomMarker: Boolean
})

defineEmits(['like', 'reply', 'delete', 'refresh'])

const {
    editingId,
    editText,
    saving,
    error,
    startEdit,
    cancelEdit,
    saveEdit
} = useCommentActions()
</script>
