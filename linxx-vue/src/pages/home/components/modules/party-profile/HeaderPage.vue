<template>
    <div class="w-full bg-gradient-to-br from-gray-100 to-red-100 dark:from-gray-900 dark:to-gray-800 py-3 px-3 sm:px-6">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-4 sm:p-5 w-full max-w-5xl mx-auto border-t-8 border-red-500 dark:border-red-400">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">

                <!-- 🔹 Logo -->
                <div class="flex justify-start pl-6">
                    <div class="w-36 h-36 sm:w-40 sm:h-40 rounded-full border-4 border-red-500 dark:border-red-400 shadow-md overflow-hidden bg-white dark:bg-gray-800 p-1">
                        <img :src="party.logo_url" alt="لوگو حزب" class="w-full h-full object-contain" />
                    </div>
                </div>

                <!-- 🔹 Info -->
                <div class="text-left space-y-2">
                    <h1 class="text-[clamp(1rem,4vw,1.3rem)] font-extrabold text-gray-900 dark:text-white leading-tight">
                        {{ party.group_name }}
                    </h1>

                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-snug">
                        {{ party.tagline }}
                    </p>

                    <!-- 🔹 Links -->
                    <div class="flex gap-3 mt-2 flex-wrap items-center">
                        <div v-for="item in socialPlatforms" :key="item.key">
                            <component
                                :is="item.icon"
                                class="w-6 h-6 sm:w-6 sm:h-6"
                                :class="{
                  'text-red-500 hover:text-red-700 dark:hover:text-red-400 transition cursor-pointer': hasLink(item.key),
                  'text-gray-400 cursor-not-allowed opacity-40': !hasLink(item.key)
                }"
                                :title="item.label"
                                @click="hasLink(item.key) && openLink(item.key)"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import {
    Globe, Twitter, Facebook, Instagram
} from 'lucide-vue-next'
import { h } from 'vue'
const TelegramIcon = {
    name: 'TelegramIcon',
    render() {
        return h('svg', {
            xmlns: 'http://www.w3.org/2000/svg',
            viewBox: '0 0 24 24',
            fill: 'currentColor',
            class: 'w-6 h-6'
        }, [
            h('path', {
                d: 'M9.743 16.18l-.39 4.67c.558 0 .798-.24 1.092-.528l2.616-2.47 5.43 3.964c.996.55 1.71.26 1.98-.918l3.6-16.896c.306-1.402-.534-2.04-1.494-1.692l-21.03 8.106c-1.44.558-1.422 1.344-.246 1.698l5.382 1.68 12.492-7.89c.588-.384 1.128-.174.684.21L9.744 16.18z'
            })
        ])
    }
}


export default {
    name: 'PartyHeaderCard',

    props: {
        party: {
            type: Object,
            required: true

        }
    },
    computed: {
        socialPlatforms() {
            return [
                { key: 'website', label: 'وب‌سایت', icon: Globe },
                { key: 'twitter', label: 'توییتر', icon: Twitter },
                { key: 'facebook', label: 'فیسبوک', icon: Facebook },
                { key: 'instagram', label: 'اینستاگرام', icon: Instagram },
                { key: 'telegram', label: 'تلگرام', icon: TelegramIcon }

            ]
        }
    },

    methods: {
        hasLink(key) {
            return this.party.links?.some(
                l => l.title?.toLowerCase() === key
            )
        },
        openLink(key) {
            const link = this.party.links.find(
                l => l.title?.toLowerCase() === key
            )
            if (link?.url) {
                window.open(link.url, '_blank')
            }
        }
    }

}

</script>
