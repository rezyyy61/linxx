<template>
    <div class="min-h-[90vh] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-4xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-3xl shadow-xl p-8 space-y-12">

            <!-- Header -->
            <div class="text-center space-y-2">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    {{ $t('post.create_title') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('post.create_subtitle') }}
                </p>
            </div>

            <!-- Post Text -->
            <section class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <MessageSquareIcon class="w-4 h-4" />
                    {{ $t('post.text_label') }}
                </label>
                <textarea
                    v-model="postStore.postText"
                    rows="6"
                    :placeholder="$t('post.text_placeholder')"
                    class="w-full text-base bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl p-5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none"
                ></textarea>
            </section>

            <!-- Media Uploads -->
            <section class="space-y-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white border-b pb-2">
                    {{ $t('post.media_section') }}
                </h3>

                <div class="grid gap-6 sm:grid-cols-2">
                    <!-- Image -->
                    <MediaUploadBox
                        icon="image"
                        labelKey="post.upload_images"
                        accept="image/*"
                        :disabled="isDisabled('image')"
                        multiple
                        @change="handleImageUpload"
                    />

                    <!-- Video -->
                    <MediaUploadBox
                        icon="video"
                        labelKey="post.upload_video"
                        accept="video/*"
                        :disabled="isDisabled('video')"
                        @change="handleVideoUpload"
                    />

                    <!-- Audio -->
                    <MediaUploadBox
                        icon="audio"
                        labelKey="post.upload_audio"
                        accept="audio/*"
                        :disabled="isDisabled('audio')"
                        @change="handleAudioUpload"
                    />

                    <!-- File -->
                    <MediaUploadBox
                        icon="file"
                        labelKey="post.upload_files"
                        accept="*"
                        :disabled="isDisabled('file')"
                        multiple
                        @change="handleFileUpload"
                    />
                </div>
            </section>

            <!-- Submit & Preview -->
            <section class="flex items-center justify-between">
                <button @click="showPreview = !showPreview" type="button" class="text-sm text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                    {{ showPreview ? $t('post.hide_preview') : $t('post.show_preview') }}
                </button>

                <button
                    :disabled="isLoading"
                    @click="submit"
                    type="button"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold shadow-lg transition-all"
                >
                    <SendIcon class="w-5 h-5" />
                    <span v-if="!isLoading">{{ $t('post.submit_button') }}</span>
                    <span v-else>{{ $t('post.submitting') }}</span>
                </button>
            </section>

            <!-- Preview -->
            <PostPreview
                v-if="showPreview"
                :postText="postStore.postText"
                :images="postStore.images"
                :videos="postStore.videos"
                :files="postStore.files"
                :audio="postStore.audio"
                @removeImage="index => postStore.images.splice(index, 1)"
                @removeVideo="index => postStore.videos.splice(index, 1)"
                @removeFile="index => postStore.files.splice(index, 1)"
                @removeAudio="postStore.audio = null"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
    MessageSquare as MessageSquareIcon,
    Send as SendIcon
} from 'lucide-vue-next'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import PostPreview from '@/pages/admin/sections/Post/PostPreview.vue'
import MediaUploadBox from "@/pages/admin/sections/Post/MediaUploadBox.vue";
import { usePostStore } from '@/stores/post'

const postStore = usePostStore()
const toast = useToast()
const { t } = useI18n()

const isLoading = ref(false)
const showPreview = ref(false)

const mediaSelected = computed(() => {
    if (postStore.images.length) return 'image'
    if (postStore.videos.length) return 'video'
    if (postStore.audio) return 'audio'
    if (postStore.files.length) return 'file'
    return null
})

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
        showPreview.value = false
    } catch (err) {
        toast.error(t('post.submit_error'))
    } finally {
        isLoading.value = false
    }
}
</script>
