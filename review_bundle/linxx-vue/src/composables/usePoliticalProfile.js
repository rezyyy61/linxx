import { usePoliticalProfileStore } from '@/stores/politicalProfile'
import {
  createPoliticalProfile,
  updatePoliticalProfile,
  getMyPoliticalProfile, listPoliticalProfiles
} from '@/api/politicalProfile'

export function usePoliticalProfileSubmit () {
  const store = usePoliticalProfileStore()

  const buildFormData = () => {
    const form = new FormData()

    // General
    form.append('group_name', store.general.groupName)
    form.append('tagline', store.general.tagline || '')
    form.append('entity_type', store.general.entityType)
    form.append('location', store.general.location || '')
    form.append('founded_year', store.general.foundedYear || '')
    if (store.general.logo) {
      form.append('logo', store.general.logo)
    }

    // Ideologies
    store.ideologies.ideologies.forEach((item, i) => {
      const value = typeof item === 'string' ? item : item.value
      const type = typeof item === 'string' ? 'custom' : item.type || 'custom'
      form.append(`ideologies[${i}][value]`, value)
      form.append(`ideologies[${i}][type]`, type)
    })

    // Description
    form.append('about', store.description.about)
    form.append('goals', store.description.goals)
    form.append('activities', store.description.activities)
    form.append('structure', store.description.structure)

    // Files
    const sections = ['about', 'goals', 'activities', 'structure']
    let index = 0
    sections.forEach(section => {
      const files = store.description[`${section}Files`] || []
      files.forEach(file => {
        form.append(`files[${index}][section]`, section)
        form.append(`files[${index}][file]`, file)
        index++
      })
    })

    // Links
    Object.entries(store.links.static).forEach(([key, url]) => {
      if (url) {
        form.append('links[]', JSON.stringify({ title: key, url }))
      }
    })

    store.links.custom.forEach(link => {
      form.append('links[]', JSON.stringify(link))
    })

    return form
  }

  const submit = async (mode = 'create', id = null) => {
    const formData = buildFormData()
    return mode === 'create'
      ? await createPoliticalProfile(formData)
      : await updatePoliticalProfile(id, formData)
  }

  const loadProfile = async () => {
    try {
      const response = await getMyPoliticalProfile()
      const data = response.data.data

      if (!data) {
        console.warn('⚠️ No profile found for current user.')
        return
      }

      // 🟥 General
      store.saveGeneral({
        id: data.id,
        groupName: data.group_name,
        tagline: data.tagline,
        entityType: data.entity_type,
        location: data.location,
        foundedYear: data.founded_year,
        avatarColor: data.avatar_color || null,
        logo: null,
        logoPreview: data.logo_url || null
      })

      // 🟨 Ideologies
      store.saveIdeologies({
        ideologies: data.ideologies?.map(i => ({
          value: i.value,
          type: i.type
        })) || [],
        keywords: []
      })

      // 🟦 Description
      const desc = {
        about: data.about,
        goals: data.goals,
        activities: data.activities,
        structure: data.structure,
        aboutFiles: [],
        goalsFiles: [],
        activitiesFiles: [],
        structureFiles: []
      }

      for (const f of data.files || []) {
        const key = `${f.section}Files`
        if (desc[key]) {
          desc[key].push({
            id: f.id,
            name: f.name,
            url: f.url,
            mimeType: f.mime_type
          })
        }
      }

      store.saveDescription(desc)

      // 🟧 Links
      const staticLinks = {}
      const customLinks = []

      for (const link of data.links || []) {
        if (['website', 'twitter', 'facebook', 'instagram', 'telegram', 'email', 'phone'].includes(link.title)) {
          staticLinks[link.title] = link.url
        } else {
          customLinks.push({ title: link.title, url: link.url })
        }
      }

      store.saveLinks({
        static: staticLinks,
        custom: customLinks
      })
    } catch (err) {
      console.error('❌ Load profile failed:', err)
      throw err
    }
  }

  const loadAllPoliticalProfiles = async () => {
    try {
      const res = await listPoliticalProfiles()
      store.saveAllProfiles(res.data.data || [])
    } catch (err) {
      console.error('❌ Failed to load all political profiles:', err)
    }
  }

  return { submit, loadProfile, loadAllPoliticalProfiles }
}
