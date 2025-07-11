<template>
    <div v-if="party" class="space-y-10">
        <!-- Header -->
        <HeaderPage :party="party" />

        <PartyAbout
            :about="party.about"
            :goals="party.goals"
            :activities="party.activities"
            :structure="party.structure"
            :files="party.files"
        />

        <PartyPublication />
    </div>

    <div v-else class="text-center text-gray-400 dark:text-gray-500 py-12">
        {{ $t('home.party.loading') }}
    </div>
</template>

<script>
import { getPoliticalProfile } from '@/api/politicalProfile'
import HeaderPage from './HeaderPage.vue'
import PartyAbout from "./PartyAbout.vue";
import PartyPublication from "./PartyPublication.vue";

export default {
    name: 'PartyProfile',
    components: {
        PartyPublication,
        PartyAbout,
        HeaderPage,
    },
    data() {
        return {
            party: null
        }
    },
    async created() {
        const id = this.$route.params.id

        try {
            const res = await getPoliticalProfile(id)
            this.party = res.data.data
        } catch (err) {
            console.error('❌ Error loading party data:', err)
        }
    }
}
</script>
