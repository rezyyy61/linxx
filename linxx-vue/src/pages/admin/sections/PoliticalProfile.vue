<template>
    <div
        class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 transition-all duration-300"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <!-- Title -->
        <h1
            class="text-2xl sm:text-3xl font-extrabold mb-8 lg:mb-10
           text-red-700 dark:text-red-400 tracking-wide"
        >
            {{ $t('dashboard.political_profile') }}
        </h1>

        <!-- Stepper Buttons -->
        <div
            class="flex flex-wrap sm:flex-nowrap lg:grid gap-3 sm:gap-4 mb-8
             overflow-x-visible sm:overflow-x-auto lg:overflow-visible
             pb-2 lg:pb-0 scrollbar-hide items-center
             lg:grid-cols-[repeat(auto-fit,minmax(180px,1fr))]
             sticky top-0 z-30 sm:static
             bg-white/90 dark:bg-gray-800/90 backdrop-blur shadow-sm"
        >
            <button
                v-for="(section, index) in sections"
                :key="section.name"
                @click="currentStep = index"
                class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200
               border-2 whitespace-nowrap w-full sm:w-auto flex-shrink-0
               lg:justify-center"
                :class="{
          'border-red-600 text-red-600 bg-white dark:bg-gray-800 shadow': currentStep === index,
          'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-red-500 hover:text-red-500':
            currentStep !== index
        }"
            >
                <component :is="section.icon" class="w-4 h-4 shrink-0" />
                <span class="text-sm sm:text-base font-medium">
          {{ index + 1 }}. {{ $t(section.title) }}
        </span>
            </button>
        </div>

        <!-- Section Form -->
        <div
            class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-lg animate-fade-in"
        >
            <component :is="sections[currentStep].component" />
        </div>
    </div>
</template>

<script setup>
import {ref, defineAsyncComponent, onMounted} from 'vue'
import { Info, Brain, FileText, Link, Send } from 'lucide-vue-next'
import {usePoliticalProfileSubmit} from "@/composables/usePoliticalProfile";
import {useAuthStore} from "@/stores/auth";

const { loadProfile } = usePoliticalProfileSubmit()

onMounted(async () => {
    const auth = useAuthStore()
    if (!auth.user) {
        await auth.fetchUser()
    }
    await loadProfile()
})


const currentStep = ref(0)

const sections = [
    {
        name: 'general',
        title: 'politicalProfile.sections.general',
        icon: Info,
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionGeneral.vue')
        )
    },
    {
        name: 'ideologies',
        title: 'politicalProfile.sections.ideologies',
        icon: Brain,
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionIdeologies.vue')
        )
    },
    {
        name: 'description',
        title: 'politicalProfile.sections.description',
        icon: FileText,
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionDescription.vue')
        )
    },
    {
        name: 'links',
        title: 'politicalProfile.sections.links',
        icon: Link,
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionLinks.vue')
        )
    },
    {
        name: 'submit',
        title: 'politicalProfile.sections.submit',
        icon: Send,
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionSubmit.vue')
        )
    }
]
</script>

<style scoped>
/* hide scrollbar */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* fade-in animation */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fade-in 0.4s ease-out;
}
</style>
