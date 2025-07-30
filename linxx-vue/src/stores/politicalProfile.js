// src/stores/politicalProfile.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import {getMyProfile} from "@/api/politicalProfile";

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
  const allProfiles = ref([])

    const myProfile = ref(null)

    async function fetchMyProfile () {
        try {
            const res = await getMyProfile()
            myProfile.value = res.data.data
            console.log(myProfile.value)
        } catch (error) {
            console.error('❌ خطا در دریافت پروفایل:', error)
            myProfile.value = null
        }
    }

  // 🟥 General
  function saveGeneral (data) {
    general.value = { ...data }
  }
  function resetGeneral () {
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
  function getGeneral () {
    return general.value
  }

  // 🟨 Ideologies
  function saveIdeologies (data) {
    ideologies.value = { ...data }
  }
  function resetIdeologies () {
    ideologies.value = {
      ideologies: [],
      keywords: []
    }
  }
  function getIdeologies () {
    return ideologies.value
  }

  // 🟦 Description
  function saveDescription (data) {
    description.value = { ...data }
  }
  function resetDescription () {
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
  function getDescription () {
    return description.value
  }

  // 🟧 Links
  function saveLinks (data) {
    links.value = { ...data }
  }
  function resetLinks () {
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
  function getLinks () {
    return links.value
  }

  function saveAllProfiles (data) {
    allProfiles.value = data
  }

  function resetAllProfiles () {
    allProfiles.value = []
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
    getLinks,

    // All Profiles
    allProfiles,
    saveAllProfiles,
    resetAllProfiles,

      myProfile,
      fetchMyProfile

  }
})
