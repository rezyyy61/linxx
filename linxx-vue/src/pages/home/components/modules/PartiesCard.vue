<template>
    <div class="space-y-6" :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'">
        <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white tracking-tight">
            {{ $t('politicalProfile.list.title') }}
        </h2>

        <div v-if="!parties.length" class="text-sm text-gray-400 dark:text-gray-500">
            {{ $t('politicalProfile.list.loading') }}
        </div>

        <div v-else class="grid gap-4">
            <div
                v-for="party in parties"
                :key="party.id"
                @click="goToParty(party.id)"
                class="group flex items-center justify-between gap-6 p-6 sm:p-7 rounded-2xl shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer
                    bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 border border-gray-100 dark:border-gray-800"
            >
                <!-- 🔹 Info -->
                <div class="flex items-center gap-4">
                    <div class="p-[3px] rounded-full bg-gradient-to-tr from-red-400 to-red-600 dark:from-red-500 dark:to-red-700">
                        <img
                            :src="party.logo_url"
                            class="w-20 h-20 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md group-hover:scale-105 transition-transform duration-300"
                            alt="لوگو"
                        />
                    </div>

                    <div class="space-y-1">
                        <div class="text-base font-bold text-gray-900 dark:text-white group-hover:text-red-500 dark:group-hover:text-red-400 transition">
                            {{ party.group_name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ party.tagline }}
                        </div>
                    </div>
                </div>

                <!-- 🔹 Links -->
                <div class="flex items-center gap-3 flex-wrap text-sm text-gray-600 dark:text-gray-300">
                    <a
                        v-for="(link, index) in party.links.slice(0, 3)"
                        :key="index"
                        :href="link.url"
                        target="_blank"
                        class="flex items-center gap-1 hover:text-red-600 dark:hover:text-red-400 transition"
                        @click.stop
                    >
                        <component :is="getIconComponent(link.title)" class="w-4 h-4" />
                        <span class="hidden sm:inline">{{ link.title }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { onMounted, computed } from 'vue'
import { usePoliticalProfileStore } from '@/stores/politicalProfile'
import { listPoliticalProfiles } from '@/api/politicalProfile'
import { useRouter } from 'vue-router'

import {
    Globe,
    Twitter,
    Facebook,
    Instagram,
    Telegram,
    Mail,
    Phone,
    Link as LinkIcon
} from 'lucide-vue-next'

const store = usePoliticalProfileStore()
const router = useRouter()

const parties = computed(() => store.allProfiles)

const goToParty = (id) => {
    router.push({ name: 'party_profile', params: { id } })
}

onMounted(async () => {
    if (!store.allProfiles.length) {
        try {
            const res = await listPoliticalProfiles()
            store.saveAllProfiles(res.data.data)
        } catch (e) {
            console.error('❌ خطا در گرفتن لیست احزاب:', e)
        }
    }
})

const getIconComponent = (title) => {
    const icons = {
        website: Globe,
        twitter: Twitter,
        facebook: Facebook,
        instagram: Instagram,
        telegram: Telegram,
        email: Mail,
        phone: Phone
    }
    return icons[title?.toLowerCase()] || LinkIcon
}
</script>
