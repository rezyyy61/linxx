<template>
    <div class="flex justify-center" :class="isModal ? 'p-0' : 'px-4 py-10'">
        <div
            class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-xl"
            :class="[isModal ? 'max-w-2xl p-6 space-y-6' : 'max-w-4xl p-8 space-y-10']"
        >
            <!-- Title -->
            <div class="text-center space-y-2">
                <h2 :class="[isModal ? 'text-2xl' : 'text-3xl', 'font-extrabold text-gray-900 dark:text-white']">
                    {{ $t('post.create_title') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('post.create_subtitle') }}
                </p>
            </div>

            <!-- Editor -->
            <section class="space-y-2">
                <TiptapEditor v-model="postStore.postText" />
            </section>

            <!-- Visibility -->
            <section class="space-y-4">
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        <Icon icon="mdi:eye-outline" class="w-5 h-5 text-gray-500" />
                        {{ $t('post.visibility_label') }}
                    </label>
                    <select v-model="visibility"
                            class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="public">{{ $t('post.visibility_public') }}</option>
                        <option value="private">{{ $t('post.visibility_private') }}</option>
                        <option value="friends">{{ $t('post.visibility_friends') }}</option>
                    </select>
                </div>
            </section>

            <!-- Media Uploads -->
            <section>
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4 mt-2">
                    <MediaUploadBox icon="image" label-key="post.upload_images" accept="image/*" :disabled="isDisabled('image')" multiple @change="handleImageUpload" />
                    <MediaUploadBox icon="video" label-key="post.upload_video" accept="video/*" :disabled="isDisabled('video')" @change="handleVideoUpload" />
                    <MediaUploadBox icon="audio" label-key="post.upload_audio" accept="audio/*" :disabled="isDisabled('audio')" @change="handleAudioUpload" />
                    <MediaUploadBox icon="file" label-key="post.upload_files" accept="*" :disabled="isDisabled('file')" multiple @change="handleFileUpload" />
                </div>
            </section>

            <!-- Archive (Moved & Styled) -->
            <section class="flex items-center justify-between p-4 rounded-xl border border-blue-100 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                <label for="archive" class="flex items-center gap-2 text-sm font-medium text-blue-900 dark:text-blue-200">
                    <Icon icon="mdi:archive-outline" class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                    {{ $t('post.archive_label') }}
                </label>
                <input
                    id="archive"
                    v-model="isArchived"
                    type="checkbox"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300 dark:border-gray-600"
                />
            </section>

            <!-- Submit -->
            <section class="flex justify-end">
                <button
                    :disabled="isLoading"
                    @click="submit"
                    type="button"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <SendIcon class="w-5 h-5" />
                    <span v-if="!isLoading">{{ $t('post.submit_button') }}</span>
                    <span v-else>{{ $t('post.submitting') }}</span>
                </button>
            </section>

            <!-- Preview -->
            <PostPreview
                v-if="hasAnyMedia"
                :images="postStore.images"
                :videos="postStore.videos"
                :files="postStore.files"
                :audio="postStore.audio"
                @remove-image="index => postStore.images.splice(index, 1)"
                @remove-video="index => postStore.videos.splice(index, 1)"
                @remove-file="index => postStore.files.splice(index, 1)"
                @remove-audio="postStore.audio = null"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
    Send as SendIcon,
} from 'lucide-vue-next'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import PostPreview from '@/pages/admin/sections/Post/PostPreview.vue'
import MediaUploadBox from '@/pages/admin/sections/Post/MediaUploadBox.vue'
import { usePostStore } from '@/stores/post'
import { Icon } from '@iconify/vue'
import TiptapEditor from "@/pages/admin/sections/TiptapEditor.vue";

const props = defineProps({
    isModal: {
        type: Boolean,
        default: false
    }
})

const postStore = usePostStore()
const toast = useToast()
const { t } = useI18n()

const isLoading = ref(false)

const visibility = computed({
    get: () => postStore.visibility,
    set: (val) => (postStore.visibility = val)
})

const isArchived = computed({
    get: () => postStore.isArchived,
    set: (val) => (postStore.isArchived = val)
})

const mediaSelected = computed(() => {
    if (postStore.images.length) return 'image'
    if (postStore.videos.length) return 'video'
    if (postStore.audio) return 'audio'
    if (postStore.files.length) return 'file'
    return null
})

const hasAnyMedia = computed(() =>
    postStore.images.length ||
    postStore.videos.length ||
    postStore.files.length ||
    postStore.audio
)

const isDisabled = (type) => mediaSelected.value && mediaSelected.value !== type

function handleImageUpload(e) {
    postStore.videos = []
    postStore.audio = null
    postStore.files = []
    const selected = Array.from(e.target.files || [])
    postStore.images = selected.map(file => ({ file, url: URL.createObjectURL(file) }))
}

function handleVideoUpload(e) {
    postStore.images = []
    postStore.audio = null
    postStore.files = []
    const file = e.target.files[0]
    if (file) postStore.videos = [{ file, url: URL.createObjectURL(file) }]
}

function handleAudioUpload(e) {
    postStore.images = []
    postStore.videos = []
    postStore.files = []
    const file = e.target.files[0]
    if (file?.type?.startsWith('audio/')) {
        postStore.audio = { file, url: URL.createObjectURL(file) }
    }
}

function handleFileUpload(e) {
    postStore.images = []
    postStore.videos = []
    postStore.audio = null
    const selected = Array.from(e.target.files || [])
    postStore.files = selected
}

async function submit() {
    isLoading.value = true
    try {
        await postStore.submitPost()
        toast.success(t('post.submit_success'))
    } catch (err) {
        toast.error(t('post.submit_error'))
    } finally {
        isLoading.value = false
    }
}
</script>
