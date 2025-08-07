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
        <Icon :icon="section.icon" class="w-4 h-4 shrink-0" />
        <span class="text-sm sm:text-base font-medium">
          {{ index + 1 }}. {{ $t(section.title) }}
        </span>
      </button>
    </div>

    <!-- Section Form -->
    <div
        class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-lg animate-fade-in"
    >
      <component
          v-if="activeSection && profileStore.profile"
          :is="activeSection.component"
          v-bind="sharedProps"
          @save="handleSaveSection"
      />
    </div>
  </div>
</template>


<script setup>
import { onMounted, ref, defineAsyncComponent, computed } from 'vue'
import { Icon } from '@iconify/vue'
import { useToast } from 'vue-toastification'
import { usePoliticalProfileStore } from '@/stores/politicalProfile/politicalProfile'

const profileStore = usePoliticalProfileStore()
const currentStep = ref(0)
const toast = useToast()


const sections = [
  {
    name: 'general',
    title: 'politicalProfile.sections.general',
    icon: 'mdi:information-outline',
    component: defineAsyncComponent(() =>
        import('@/pages/admin/sections/Profile/SectionGeneral.vue')
    )
  },
  {
    name: 'ideologies',
    title: 'politicalProfile.sections.ideologies',
    icon: 'mdi:brain',
    component: defineAsyncComponent(() =>
        import('@/pages/admin/sections/Profile/SectionIdeologies.vue')
    )
  },
  {
    name: 'description',
    title: 'politicalProfile.sections.description',
    icon: 'mdi:file-document-outline',
    component: defineAsyncComponent(() =>
        import('@/pages/admin/sections/Profile/SectionDescription.vue')
    )
  },
  {
    name: 'links',
    title: 'politicalProfile.sections.links',
    icon: 'mdi:link-variant',
    component: defineAsyncComponent(() =>
        import('@/pages/admin/sections/Profile/SectionLinks.vue')
    )
  }
]

const activeSection = computed(() => sections[currentStep.value] || null)

const sharedProps = computed(() => ({
  profile: profileStore.profile,
  ideologies: profileStore.profile?.ideologies || [],
  keywords: profileStore.profile?.keywords || [],
description: {
  about: profileStore.profile?.about || '',
  goals: profileStore.profile?.goals || '',
  activities: profileStore.profile?.activities || '',
  structure: profileStore.profile?.structure || '',
  files: profileStore.profile?.files || []
  },
  links: {
    static: {
      website: profileStore.profile?.links?.find(l => l.type === 'website')?.url || '',
      youtube: profileStore.profile?.links?.find(l => l.type === 'youtube')?.url || '',
      twitter: profileStore.profile?.links?.find(l => l.type === 'twitter')?.url || '',
      facebook: profileStore.profile?.links?.find(l => l.type === 'facebook')?.url || '',
      instagram: profileStore.profile?.links?.find(l => l.type === 'instagram')?.url || '',
      telegram: profileStore.profile?.links?.find(l => l.type === 'telegram')?.url || '',
      email: profileStore.profile?.links?.find(l => l.type === 'email')?.url || '',
      phone: profileStore.profile?.links?.find(l => l.type === 'phone')?.url || ''
    },
    custom: profileStore.profile?.links
        ?.filter(l => l.type === 'custom')
        .map(l => ({
          id: l.id,
          title: l.title || '',
          url: l.url
        })) || []
  }
}))

async function handleSaveSection(payload) {
  try {
    await profileStore.saveSection(payload, toast)
    console.log('✅ Section saved successfully', payload)
  } catch (err) {
    console.error('❌ Failed to save section', err)
  }
}

onMounted(() => {
  profileStore.fetchMyProfile()
})
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
