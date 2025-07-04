<template>
    <div class="space-y-12">
        <div v-for="field in fields" :key="field.key" class="space-y-4">
            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                {{ $t(`politicalProfile.description.${field.key}`) }}
                <span
                    v-if="field.optional"
                    class="text-sm text-gray-400 dark:text-gray-400 font-normal ml-2"
                >
          ({{ $t('politicalProfile.description.optional') }})
        </span>
            </h3>

            <!-- Upload Hint -->
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="flex items-center gap-2 text-red-600 hover:text-red-700 font-medium transition"
                    @click="() => triggerFileInput(field.key)"
                >
                    📎
                    <span class="text-sm">
            {{ $t('politicalProfile.description.uploadHint') }}
          </span>
                </button>
            </div>

            <!-- Textarea -->
            <textarea
                v-model="store.description[field.key]"
                class="rich-textarea"
            ></textarea>

            <!-- File Upload Input -->
            <input
                type="file"
                multiple
                accept=".pdf,.doc,.docx,.jpg,.png,.jpeg"
                :ref="el => setFileInputRef(field.key, el)"
                class="hidden"
                @change="e => handleMultiFileUpload(e, field.key)"
            />

            <!-- File Previews -->
            <div
                v-if="store.description[`${field.key}Files`]?.length"
                class="flex flex-wrap gap-4 mt-3"
            >
                <div
                    v-for="(file, index) in store.description[`${field.key}Files`]"
                    :key="index"
                    class="relative w-32 h-32 border rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden"
                >
                    <!-- Image preview -->
                    <img
                        v-if="isImage(file)"
                        :src="filePreviewUrl(file)"
                        class="object-cover w-full h-full"
                        alt="uploaded image"
                    />
                    <!-- Non-image file -->
                    <div v-else class="text-sm p-2 text-center text-gray-800 dark:text-gray-300">
                        📄 {{ file.name }}
                    </div>

                    <!-- Remove Button -->
                    <button
                        class="absolute top-1 right-1 text-red-500 hover:text-red-700 text-sm bg-white dark:bg-gray-700 rounded-full p-1 shadow"
                        @click="() => removeFile(field.key, index)"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-6">
            <button
                type="button"
                @click="handleSave"
                class="flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition"
            >
                {{ $t('politicalProfile.general.save') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { usePoliticalProfileStore } from '@/stores/politicalProfile'
import { reactive } from 'vue'

const store = usePoliticalProfileStore()

/* ---------- refs برای input فایل‌ها ---------- */
const fileInputs = reactive({})
const setFileInputRef = (key, el) => { if (el) fileInputs[key] = el }
const triggerFileInput = k => fileInputs[k]?.click()

/* ---------- افزودن فایل‌ها (حداکثر ۵) ---------- */
function handleMultiFileUpload (e, key) {
    const newFiles   = Array.from(e.target.files).slice(0, 5)
    const currentArr = store.description[`${key}Files`] || []
    const freeSlots  = 5 - currentArr.length
    store.description[`${key}Files`] = [
        ...currentArr,
        ...newFiles.slice(0, freeSlots)
    ]
}

/* ---------- حذف یک فایل ---------- */
function removeFile (key, idx) {
    const arr = store.description[`${key}Files`]
    if (arr) arr.splice(idx, 1)
}

/* ---------- ابزارهای کمکی ---------- */
const fields = [
    { key: 'about' },
    { key: 'goals' },
    { key: 'activities' },
    { key: 'structure', optional: true }
]
const isImage = f => f?.type?.startsWith('image/')
const filePreviewUrl = f => URL.createObjectURL(f)

/* تبدیل File به Base64 */
const toBase64 = file =>
    new Promise(res => {
        const reader = new FileReader()
        reader.onload = () => res(reader.result) // data:url
        reader.readAsDataURL(file)
    })

/* ‌ساخت نسخه‌ی «سبک» برای ذخیره */
const meta = f => ({ name: f.name, size: f.size, type: f.type })

/* ---------- ذخیره (Pinia + LocalStorage با فایل Base64) ---------- */
async function handleSave () {
    /* 1️⃣ ذخیرهٔ کامل در Pinia */
    store.saveDescription({ ...store.description })

    /* 2️⃣ تبدیل فایل‌ها به Base64 و ذخیره در LocalStorage */
    const lightCopy = JSON.parse(JSON.stringify(store.description)) // متن‌ها
    for (const k of ['about', 'goals', 'activities', 'structure']) {
        const files = store.description[`${k}Files`] || []
        lightCopy[`${k}Files`] = await Promise.all(
            files.map(async f => ({
                ...meta(f),
                data: await toBase64(f)       // ⬅ Base64
            }))
        )
    }
    localStorage.setItem('descriptionDraft', JSON.stringify(lightCopy))
    console.log('✅ Saved to Pinia + LocalStorage (Base64):', lightCopy)
}
</script>


<style scoped>
.rich-textarea {
    @apply w-full px-6 py-5 border border-gray-300 dark:border-gray-700 rounded-xl
    bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400
    focus:outline-none focus:ring-2 focus:ring-red-500 resize-y transition
    text-[1.1rem] leading-loose min-h-[160px] max-h-[400px] overflow-y-auto shadow-sm;
}
</style>
