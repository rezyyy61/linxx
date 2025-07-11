<template>
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Container -->
        <div class="flex-1 overflow-hidden">
            <div class="p-6 space-y-8 max-w-7xl mx-auto h-full flex flex-col">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h1 class="text-2xl sm:text-3xl font-extrabold mb-8 lg:mb-10
           text-red-700 dark:text-red-400 tracking-wide">
                        {{ $t('dashboard.publication.title') }}
                    </h1>
                    <button
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-full hover:bg-blue-700 transition shadow-md"
                        @click="currentView = 'upload'"
                    >
                        <PlusIcon class="w-5 h-5" />
                        <span class="text-sm font-semibold">
              {{ $t('dashboard.publication.new') }}
            </span>
                    </button>
                </div>

                <!-- Body -->
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6 overflow-y-auto flex-1 min-h-0"
                >
                    <component :is="currentViewComponent" @uploaded="handleUploaded" />
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, computed } from 'vue'
import { Plus as PlusIcon } from 'lucide-vue-next'

// Components
import PublicationForm from './publications/PublicationForm.vue'
import PublicationList from './publications/PublicationList.vue'

const currentView = ref('list')

const views = {
    list: PublicationList,
    upload: PublicationForm
}

const currentViewComponent = computed(() => views[currentView.value])

function handleUploaded() {
    currentView.value = 'list'
}
</script>
