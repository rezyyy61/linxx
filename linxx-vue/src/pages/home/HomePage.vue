<template>
    <div class="min-h-screen bg-gray-100 dark:bg-[#0f0f0f]">
        <MainNavbar />
        <AdvancedNavbar />
    </div>
</template>

<script>

import MainNavbar from "@/pages/home/components/MainNavbar.vue";
import AdvancedNavbar from "@/pages/home/components/AdvancedNavbar.vue";

export default {
    name: "HomePage",
    components: {
        MainNavbar,
        AdvancedNavbar,
    }
};
</script>
