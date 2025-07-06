<template>
    <section
        class="bg-white dark:bg-gray-900   p-6 sm:p-8 space-y-6 border-l border-r border-gray-200 dark:border-gray-700"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >

    <h2 class="text-xl font-bold text-red-600 dark:text-red-400 border-b border-red-300 dark:border-red-500 pb-2">
            {{ $t('politicalProfile.description-public.about_title') }}
        </h2>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-3 text-sm font-medium">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="selectedTab = tab.key"
                :class="[
      'px-4 py-1.5 rounded-full transition',
      selectedTab === tab.key
        ? 'bg-red-500 text-white dark:bg-red-400'
        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
    ]"
            >
                {{ $t(`politicalProfile.description-public.${tab.key}`) }}
            </button>
        </div>


        <!-- Content -->
        <div v-if="currentTabContent || currentSectionFiles.length" class="space-y-4">
            <p
                v-if="currentTabContent"
                class="scroll-box text-lg leading-relaxed text-gray-700 dark:text-gray-300 max-h-64 overflow-y-auto pr-2"
                dir="auto"
            >
                {{ currentTabContent }}
            </p>


            <!-- Attached Files (Better UI) -->
            <div v-if="currentSectionFiles.length" class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white">
                    {{ $t('politicalProfile.description-public.attachments') }}
                </h4>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="file in currentSectionFiles"
                        :key="file.url"
                        class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow transition"
                    >
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900">📄</div>
                        <div class="flex-1 overflow-hidden">
                            <a
                                :href="file.url"
                                target="_blank"
                                class="text-sm font-medium text-gray-900 dark:text-white truncate hover:underline"
                            >
                                {{ file.name }}
                            </a>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ file.mime_type }}
                            </p>
                        </div>
                        <a
                            :href="file.url"
                            target="_blank"
                            class="text-red-500 hover:text-red-700 dark:hover:text-red-300"
                            :title="$t('politicalProfile.description-public.download')"
                        >
                            ⬇️
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Content -->
        <div v-else class="text-sm text-gray-400 dark:text-gray-500">
            {{ $t('politicalProfile.description-public.no_content') }}
        </div>
    </section>
</template>

<script>
export default {
    name: 'PartyAbout',
    props: {
        about: String,
        goals: String,
        activities: String,
        structure: String,
        files: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            selectedTab: 'about',
            tabs: [
                { key: 'about' },
                { key: 'goals' },
                { key: 'activities' },
                { key: 'structure' }
            ]

        }
    },
    computed: {
        currentTabContent() {
            return this[this.selectedTab]
        },
        currentSectionFiles() {
            return this.files.filter(file => file.section === this.selectedTab)
        }
    }
}
</script>

<style>
/* فایل main CSS یا Tailwind layer */
.scroll-box::-webkit-scrollbar {
    width: 6px;
}

.scroll-box::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.4);
    border-radius: 9999px;
}

.scroll-box::-webkit-scrollbar-track {
    background: transparent;
}

</style>
