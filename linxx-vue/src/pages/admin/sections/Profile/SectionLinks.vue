<template>
    <div class="space-y-12">
        <!-- عنوان اصلی -->
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ $t('politicalProfile.links.title') }}
        </h2>

        <!-- فیلدهای ارتباطی ثابت -->
        <div class="grid md:grid-cols-2 gap-6">
            <div
                v-for="item in staticFields"
                :key="item.key"
                class="space-y-2"
            >
                <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <component :is="item.icon" class="w-5 h-5" />
                    {{ $t(`politicalProfile.contact.${item.i18nKey}`) }}
                </label>
                <input
                    :type="item.type"
                    v-model="store.links.static[item.key]"
                    class="form-input"
                    :placeholder="$t(`politicalProfile.contact.placeholders.${item.placeholderKey}`)"
                />
            </div>
        </div>

        <!-- لینک‌های سفارشی -->
        <div class="space-y-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ $t('politicalProfile.links.customLinks') }}
            </h3>

            <!-- لیست -->
            <div v-if="store.links.custom.length" class="space-y-4">
                <div
                    v-for="(link, index) in store.links.custom"
                    :key="index"
                    class="flex items-center justify-between bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-600"
                >
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white">{{ link.title }}</p>
                        <a
                            :href="link.url"
                            target="_blank"
                            class="text-sm text-blue-600 hover:underline break-words"
                        >
                            {{ link.url }}
                        </a>
                    </div>
                    <button
                        @click="removeLink(index)"
                        class="text-red-500 hover:text-red-700 transition"
                        :title="$t('politicalProfile.links.remove')"
                    >
                        ✕
                    </button>
                </div>
            </div>

            <!-- فرم افزودن -->
            <form @submit.prevent="addLink" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <input
                        v-model="newLink.title"
                        type="text"
                        class="form-input"
                        :placeholder="$t('politicalProfile.links.placeholders.title')"
                    />
                    <input
                        v-model="newLink.url"
                        type="url"
                        class="form-input"
                        :placeholder="$t('politicalProfile.links.placeholders.url')"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <button
                        type="submit"
                        class="btn-red"
                        :disabled="!isValidLink"
                    >
                        {{ $t('politicalProfile.links.addButton') }}
                    </button>
                    <span v-if="error" class="text-sm text-red-500">{{ error }}</span>
                </div>
            </form>
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
import { ref, computed, reactive } from 'vue'
import { usePoliticalProfileStore } from '@/stores/politicalProfile'
import {
    Globe2,
    Twitter,
    Facebook,
    Instagram,
    Mail,
    Phone
} from 'lucide-vue-next'

const store = usePoliticalProfileStore()

const staticFields = [
    { key: 'website', i18nKey: 'website', type: 'url', placeholderKey: 'website', icon: Globe2 },
    { key: 'twitter', i18nKey: 'twitter', type: 'url', placeholderKey: 'twitter', icon: Twitter },
    { key: 'facebook', i18nKey: 'facebook', type: 'url', placeholderKey: 'facebook', icon: Facebook },
    { key: 'instagram', i18nKey: 'instagram', type: 'url', placeholderKey: 'instagram', icon: Instagram },
    { key: 'telegram', i18nKey: 'telegram', type: 'url', placeholderKey: 'telegram', icon: Globe2 },
    { key: 'email', i18nKey: 'email', type: 'email', placeholderKey: 'email', icon: Mail },
    { key: 'phone', i18nKey: 'phone', type: 'text', placeholderKey: 'phone', icon: Phone }
]

// بخش لینک سفارشی
const newLink = reactive({ title: '', url: '' })
const error = ref('')

const isValidLink = computed(() =>
    newLink.title.trim() && /^https?:\/\/.+/.test(newLink.url)
)

function addLink() {
    if (!isValidLink.value) {
        error.value = 'لینک معتبر وارد کنید.'
        return
    }
    store.links.custom.push({ ...newLink })
    newLink.title = ''
    newLink.url = ''
    error.value = ''
}

function removeLink(index) {
    store.links.custom.splice(index, 1)
}

const handleSave = () => {
    store.saveLinks({ ...store.links })
    console.log('✅ Links saved in Pinia:', store.links)
}
</script>

<style scoped>
.form-input {
    @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg
    bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400
    focus:outline-none focus:ring-2 focus:ring-red-500 transition;
}
.btn-red {
    @apply px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition;
}
</style>
