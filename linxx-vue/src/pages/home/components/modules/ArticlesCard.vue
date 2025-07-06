<template>
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">لیست احزاب</h2>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
                v-for="party in parties"
                :key="party.id"
                class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer rounded transition"
                @click="goToParty(party.id)"
            >
                <div class="flex items-center gap-4">
                    <img :src="party.logo_url" class="w-12 h-12 rounded-full object-cover border" />
                    <div>
                        <h3 class="text-base font-semibold">{{ party.group_name }}</h3>
                        <p class="text-sm text-gray-500">{{ party.tagline }}</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 text-right">
                    📍 {{ party.location }}<br />
                    📅 {{ party.founded_year }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PartiesList",
    data() {
        return {
            parties: [
                {
                    id: 18,
                    group_name: "جنبش عدالت‌خواه",
                    tagline: "مردم پیشتاز تحول",
                    founded_year: "2018",
                    location: "تهران",
                    logo_url: "https://via.placeholder.com/60x60?text=JA",
                },
                {
                    id: 2,
                    group_name: "حزب توسعه",
                    tagline: "برای آینده‌ای بهتر",
                    founded_year: "2020",
                    location: "اصفهان",
                    logo_url: "https://via.placeholder.com/60x60?text=TD",
                },
            ],
        };
    },
    methods: {
        goToParty(id) {
            this.$router.push({ name: "party_profile", params: { id } });
        },
    },
};
</script>
