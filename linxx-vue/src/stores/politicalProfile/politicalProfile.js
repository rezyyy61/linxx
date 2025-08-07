import { defineStore } from 'pinia'
import axios from '@/lib/axios'
import i18n from '@/i18n'

export const usePoliticalProfileStore = defineStore('politicalProfile', {
    state: () => ({
        profile: null,
        loading: false,
        error: null
    }),

    getters: {
        hasProfile: state => !!state.profile,
        isLoading: state => state.loading,
        getGeneral: state => state.profile
            ? {
                groupName: state.profile.group_name,
                tagline: state.profile.tagline,
                entityType: state.profile.entity_type,
                location: state.profile.location,
                foundedYear: state.profile.founded_year,
                avatarColor: state.profile.avatar_color,
                logoUrl: state.profile.logo_url
            }
            : null
    },

    actions: {
        async fetchMyProfile() {
            this.loading = true
            this.error = null
            try {
                const { data } = await axios.get('api/political-profiles/me')
                this.profile = data.data
            } catch (err) {
                console.error('❌ Failed to load profile:', err)
                this.error = err.response?.data?.message || 'Failed to load profile.'
                this.profile = null
            } finally {
                this.loading = false
            }
        },

        async saveSection(sectionData, toast) {
            this.loading = true
            this.error = null

            try {
                const formData = new FormData()
                const allowed = [
                    'group_name', 'tagline', 'entity_type', 'location',
                    'founded_year', 'avatar_color', 'logo',
                    'links', 'ideologies',
                    'files', 'removed_files', 'removed_links',
                    'about', 'goals', 'activities', 'structure'
                ]

                for (const key in sectionData) {
                    if (!allowed.includes(key)) continue
                    const val = sectionData[key]

                    if (val === undefined) continue

                    if (key === 'logo') {
                        if (val instanceof File) {
                            formData.append('logo', val)
                        } else if (val === null) {
                            formData.append('logo_removed', '1')
                        }
                        continue
                    }

                    if (key === 'files' && Array.isArray(val)) {
                        val.forEach((obj, i) => {
                            if (obj.file instanceof File) {
                                formData.append(`files[${i}][file]`, obj.file)
                            }
                            if (obj.section) {
                                formData.append(`files[${i}][section]`, obj.section)
                            }
                            if (obj.id) {
                                formData.append(`files[${i}][id]`, obj.id)
                            }
                        })
                        continue
                    }

                    if (val instanceof File) {
                        formData.append(key, val)
                        continue
                    }

                    if (Array.isArray(val) || typeof val === 'object') {
                        formData.append(key, JSON.stringify(val))
                        continue
                    }

                    formData.append(key, val)
                }

                const { data } = await axios.post('api/political-profiles', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })

                this.profile = data.data
                toast.success(i18n.global.t('politicalProfile.messages.saveSuccess'))
                return true
            } catch (e) {
                const code = e.response?.status
                if (code === 422) {
                    this.error = i18n.global.t('politicalProfile.messages.validationError')
                } else {
                    this.error = i18n.global.t('politicalProfile.messages.saveFailed')
                }
                toast.error(`❌ ${this.error}`)
                return false
            } finally {
                this.loading = false
            }
        },

        clearProfile() {
            this.profile = null
            this.error = null
        }
    }
})
