<template>
  <div class="w-full">
    <div class="overflow-x-auto scrollbar-hide">
      <nav class="flex gap-3 px-4 py-4 min-w-max md:justify-center md:min-w-0 md:mx-auto md:max-w-5xl">
        <router-link
            v-for="item in categories"
            :key="item.key"
            :to="{ name: item.key }"
            class="px-6 py-3 rounded-xl text-base font-semibold whitespace-nowrap transition-all duration-200 border border-transparent"
            :class="$route.name === item.key
            ? 'bg-red-600 text-white shadow-sm ring-1 ring-red-500/30'
            : 'bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-800/90 dark:text-gray-200 dark:hover:bg-gray-700/80'"
        >
          {{ $t(item.i18n) }}
        </router-link>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CategoryTabs',
  data () {
    return {
      categories: [
        { key: 'feed', i18n: 'home.tabs.feed' },
        { key: 'parties', i18n: 'home.tabs.parties' },
        { key: 'books', i18n: 'home.tabs.books' },
        { key: 'media', i18n: 'home.tabs.media' },
        { key: 'campaigns', i18n: 'home.tabs.campaigns' },
        { key: 'profiles', i18n: 'home.tabs.profiles' },
        { key: 'public_events', i18n: 'home.tabs.events' },
        { key: 'announcements', i18n: 'home.tabs.announcements' }
      ]
    }
  }
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
