<template>
    <div class="font-vazir max-w-4xl mx-auto p-4 animate-fade-in">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 rounded-3xl shadow-xl px-6 py-8 space-y-10">

            <!-- Info -->
            <div class="relative p-4 rounded-2xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <InfoIcon class="w-5 h-5 mt-0.5 flex-shrink-0 text-blue-500 dark:text-blue-400" />
                    <p class="text-sm leading-relaxed">
                        {{ $t('dashboard.publication.tip_message') }}
                    </p>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center space-y-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center justify-center gap-2">
                    <FileTextIcon class="w-6 h-6 text-blue-500" />
                    {{ $t('dashboard.publication.title_form') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('dashboard.publication.file_note') }}
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-10">
                <div
v-if="Object.keys(errors).length"
                     class="p-4 rounded-lg bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-100 border border-red-300 dark:border-red-600"
>
                    <h4 class="font-semibold mb-2">{{ $t('dashboard.publication.validation_failed') }}</h4>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li v-for="(msg, field) in errors" :key="field">{{ msg[0] }}</li>
                    </ul>
                </div>


                <!-- Basic Info -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white border-b pb-2 mb-4">
                        {{ $t('dashboard.publication.basic_info') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <BaseInput v-model="form.title" :label="$t('dashboard.publication.title')" required icon="Type" :error="errors.title?.[0]" />
                        <BaseInput v-model="form.issue" :label="$t('dashboard.publication.issue')" required icon="Hash" :error="errors.title?.[0]" />
                        <BaseInput v-model="form.publishedAt" :label="$t('dashboard.publication.published_at')" required type="date" icon="Calendar" :error="errors.title?.[0]" />
                    </div>
                </section>

                <!-- Description -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white border-b pb-2 mb-4">
                        {{ $t('dashboard.publication.description_title') }}
                    </h3>

                    <BaseTextarea v-model="form.description" :label="$t('dashboard.publication.description')" icon="AlignLeft" :error="errors.description?.[0]" />

                    <!-- AI Suggestion Button -->
                    <div class="flex justify-end mt-3">
                        <button
                            @click="handleSuggestDescription"
                            type="button"
                            class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-full transition-all bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-lg disabled:opacity-60"
                            :disabled="generatingDescription"
                        >
                            <span v-if="!generatingDescription">
                                {{ $t('dashboard.publication.suggest_description') }}
                            </span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path
class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
/>
                                </svg>
                                {{ $t('dashboard.publication.loading') }}
                            </span>
                        </button>
                    </div>
                </section>

                <!-- File Upload -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white border-b pb-2 mb-4">
                        {{ $t('dashboard.publication.upload_file') }}
                    </h3>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                            <UploadIcon class="w-4 h-4" />
                            {{ $t('dashboard.publication.file') }}
                        </label>

                        <div
                            class="relative border-2 border-dashed border-gray-400 dark:border-gray-600 rounded-xl p-6 cursor-pointer hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 transition group"
                            @click="triggerFileSelect"
                        >
                            <div v-if="!selectedFileName" class="text-center text-sm text-gray-600 dark:text-gray-300">
                                <UploadIcon class="w-10 h-10 mx-auto text-blue-500 mb-2 group-hover:scale-105 transition" />
                                {{ $t('dashboard.publication.file_note') }}
                            </div>

                            <div v-else class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2 relative z-10">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <component :is="fileIconComponent" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    <div class="flex flex-col truncate">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ selectedFileName }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ selectedFileSize }}</span>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click.stop.prevent="removeFile"
                                    class="flex items-center gap-1 px-3 py-1.5 rounded-md bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold shadow-sm transition-all z-20 relative"
                                    :title="$t('dashboard.publication.remove_file')"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                    {{ $t('dashboard.publication.remove_file') }}
                                </button>
                            </div>

                            <input
                                ref="fileInput"
                                type="file"
                                accept=".pdf,.doc,.docx"
                                class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                                @change="handleFileChange"
                                required
                            />
                        </div>
                    </div>
                </section>

                <!-- Uploading Progress -->
                <div v-if="uploading">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 transition-all duration-300" :style="{ width: progress + '%' }"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $t('dashboard.publication.uploading') }}</p>
                </div>

                <!-- Submit Button -->
                <div class="sticky bottom-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur rounded-xl p-4 flex justify-end shadow-xl border">
                    <button
                        type="submit"
                        :disabled="uploading"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-green-600 rounded-full hover:from-green-600 hover:to-green-700 transition-all disabled:opacity-50"
                    >
                        <CloudUploadIcon class="w-5 h-5" />
                        {{ $t('dashboard.publication.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>


<script setup>
import {ref, watch} from 'vue'
import {
    FileText as FileTextIcon,
    CloudUpload as CloudUploadIcon,
    Upload as UploadIcon,
    InfoIcon,
    TrashIcon
} from 'lucide-vue-next'

import { useI18n } from 'vue-i18n'
import BaseTextarea from '@/pages/admin/components/BaseTextarea.vue'
import BaseInput from '@/pages/admin/components/BaseInput.vue'
import { usePublicationStore } from '@/stores/publications'
import { useToast } from 'vue-toastification'

const errors = ref({})
watch(errors, (newVal) => {
    const keys = Object.keys(newVal)
    if (keys.length) {
        const firstField = document.querySelector(`[name="${keys[0]}"]`)
        firstField?.scrollIntoView({ behavior: 'smooth', block: 'center' })
        firstField?.focus()
    }
})

const { t } = useI18n()
const toast = useToast()
const emit = defineEmits(['uploaded'])
const publicationStore = usePublicationStore()

const form = ref({
    title: '',
    issue: '',
    description: '',
    publishedAt: '',
    file: null
})

const suggestedDescription = ref('')
const uploading = ref(false)
const generatingDescription = ref(false)
const progress = ref(0)

const fileInput = ref(null)
const selectedFileName = ref('')
const selectedFileSize = ref('')

function triggerFileSelect() {
    fileInput.value?.click()
}

function handleFileChange(event) {
    const file = event.target.files[0]
    if (file) {
        form.value.file = file
        selectedFileName.value = file.name
        selectedFileSize.value = formatFileSize(file.size)
    }
}

function removeFile() {
    form.value.file = null
    selectedFileName.value = ''
    selectedFileSize.value = ''
    if (fileInput.value) fileInput.value.value = ''
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

async function handleSubmit() {
    if (!form.value.title || !form.value.file) {
        toast.warning(t('dashboard.publication.missing_fields'), {
            timeout: 4000,
            position: 'top-center',
        })
        return
    }

    const fileSizeMB = form.value.file.size / (1024 * 1024)
    if (fileSizeMB > 10) {
        toast.warning(t('dashboard.publication.file_too_large'), {
            timeout: 4000,
            position: 'top-center',
        })
        return
    }

    const data = new FormData()
    data.append('title', form.value.title)
    data.append('issue', form.value.issue)
    data.append('description', form.value.description)
    data.append('published_at', form.value.publishedAt)
    data.append('file', form.value.file)

    uploading.value = true

    try {
        const response = await publicationStore.addPublication(data)
        errors.value = {}

        const suggested = response?.suggested_description
        if (!form.value.description && suggested) {
            form.value.description = suggested
            suggestedDescription.value = suggested
            toast.info(t('dashboard.publication.suggested_description_applied'), {
                timeout: 6000,
                position: 'top-center'
            })
        }

        toast.success(t('dashboard.publication.upload_success'))
        emit('uploaded')
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {}
            toast.warning(t('dashboard.publication.validation_failed'), {
                timeout: 4000,
                position: 'top-center',
            })
        } else {
            toast.error(t('dashboard.publication.upload_error'))
        }
        } finally {
        uploading.value = false
        progress.value = 0
        resetForm()
    }
}

function resetForm() {
    form.value = {
        title: '',
        issue: '',
        description: '',
        publishedAt: '',
        file: null
    }
    selectedFileName.value = ''
    selectedFileSize.value = ''
    suggestedDescription.value = ''
    if (fileInput.value) fileInput.value.value = ''
}

async function handleSuggestDescription() {
    if (!form.value.file) {
        toast('⚠️ ' + t('dashboard.publication.select_file_first'), {
            timeout: 8000,
            position: 'top-center',
            type: 'default',
            icon: false,
            closeButton: true,
            hideProgressBar: false,
            toastClassName: 'bg-gray-800 text-white font-semibold rounded-lg shadow-md px-6 py-4',
            bodyClassName: 'text-sm leading-relaxed'
        })


        return
    }

    generatingDescription.value = true

    try {
        const aiDesc = await publicationStore.getSuggestedDescription(form.value.file)
        if (aiDesc) {
            form.value.description = aiDesc
            suggestedDescription.value = aiDesc
            toast.success(t('dashboard.publication.ai_description_loaded'))
        } else {
            toast.warning(t('dashboard.publication.no_ai_response'))
        }
    } catch (err) {
        toast.error(t('dashboard.publication.ai_error'))
    } finally {
        generatingDescription.value = false
    }
}

</script>




