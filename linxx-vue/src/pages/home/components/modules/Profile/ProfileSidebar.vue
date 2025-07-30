<template>
    <div class="p-6 rounded-2xl shadow-md bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-2">
            {{ $t('politicalProfile.sidebar.title') }}
        </h3>
        <!-- اطلاعات تکمیلی -->
        <p class="text-sm text-gray-500 dark:text-gray-400">اطلاعات جانبی یا ابزارهای مدیریتی.</p>
    </div>
</template>

<script setup>
defineProps({
    user: Object
})
</script>
