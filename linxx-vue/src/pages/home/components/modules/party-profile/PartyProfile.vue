<template>
    <div v-if="party" class="space-y-10">
        <!-- سربرگ -->
        <HeaderPage :party="party" />

        <PartyAbout
            :about="party.about"
            :goals="party.goals"
            :activities="party.activities"
            :structure="party.structure"
            :files="party.files"
        />


        <!--        &lt;!&ndash; اهداف &ndash;&gt;-->
<!--        <PartyGoals :goals="party.goals" />-->

<!--        &lt;!&ndash; ایدئولوژی‌ها &ndash;&gt;-->
<!--        <PartyIdeologies :ideologies="party.ideologies" />-->

<!--        &lt;!&ndash; لینک‌ها &ndash;&gt;-->
<!--        <PartyLinks :links="party.links" />-->

<!--        &lt;!&ndash; فایل‌ها / رسانه‌ها &ndash;&gt;-->
<!--        <PartyMedia v-if="party.files?.length" :files="party.files" />-->
    </div>

    <!-- حالت لودینگ -->
    <div v-else class="text-center text-gray-400 dark:text-gray-500 py-12">
        در حال بارگذاری اطلاعات...
    </div>
</template>

<script>
import { getPoliticalProfile } from '@/api/politicalProfile'

// ایمپورت کامپوننت‌های داخلی
import HeaderPage from './HeaderPage.vue'
import PartyAbout from "./PartyAbout.vue";
// import PartyGoals from './Goals.vue'
// import PartyIdeologies from './Ideologies.vue'
// import PartyLinks from './Links.vue'
// import PartyMedia from './Media.vue'

export default {
    name: 'PartyProfile',
    components: {
        PartyAbout,
        HeaderPage,
        // PartyAbout,
        // PartyGoals,
        // PartyIdeologies,
        // PartyLinks,
        // PartyMedia
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
            console.error('❌ خطا در دریافت اطلاعات حزب:', err)
        }
    }
}
</script>
