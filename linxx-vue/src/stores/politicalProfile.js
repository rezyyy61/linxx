// src/stores/politicalProfile.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePoliticalProfileStore = defineStore('politicalProfile', () => {
    const general = ref({
        groupName: '',
        tagline: '',
        entityType: '',
        location: '',
        foundedYear: '',
        logo: null,
        logoPreview: null
    })

    const ideologies = ref({
        ideologies: [],
        keywords: []
    })

    const description = ref({
        about: '',
        goals: '',
        activities: '',
        structure: '',
        aboutFiles: [],
        goalsFiles: [],
        activitiesFiles: [],
        structureFiles: []
    })

    const links = ref({
        static: {
            website: '',
            twitter: '',
            facebook: '',
            instagram: '',
            telegram: '',
            email: '',
            phone: ''
        },
        custom: []
    })

    // 🟥 General
    function saveGeneral(data) {
        general.value = { ...data }
    }
    function resetGeneral() {
        general.value = {
            groupName: '',
            tagline: '',
            entityType: '',
            location: '',
            foundedYear: '',
            logo: null,
            logoPreview: null
        }
    }
    function getGeneral() {
        return general.value
    }

    // 🟨 Ideologies
    function saveIdeologies(data) {
        ideologies.value = { ...data }
    }
    function resetIdeologies() {
        ideologies.value = {
            ideologies: [],
            keywords: []
        }
    }
    function getIdeologies() {
        return ideologies.value
    }

    // 🟦 Description
    function saveDescription(data) {
        description.value = { ...data }
    }
    function resetDescription() {
        description.value = {
            about: '',
            goals: '',
            activities: '',
            structure: '',
            aboutFiles: [],
            goalsFiles: [],
            activitiesFiles: [],
            structureFiles: []
        }
    }
    function getDescription() {
        return description.value
    }

    // 🟧 Links
    function saveLinks(data) {
        links.value = { ...data }
    }
    function resetLinks() {
        links.value = {
            static: {
                website: '',
                twitter: '',
                facebook: '',
                instagram: '',
                telegram: '',
                email: '',
                phone: ''
            },
            custom: []
        }
    }
    function getLinks() {
        return links.value
    }

    return {
        // General
        general,
        saveGeneral,
        resetGeneral,
        getGeneral,

        // Ideologies
        ideologies,
        saveIdeologies,
        resetIdeologies,
        getIdeologies,

        // Description
        description,
        saveDescription,
        resetDescription,
        getDescription,

        // Links
        links,
        saveLinks,
        resetLinks,
        getLinks
    }
})
