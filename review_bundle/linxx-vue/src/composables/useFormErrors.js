import { ref } from 'vue'

export function useFormErrors() {
    const errors = ref({})

    function setErrorsFromResponse(error) {
        if (error?.response?.status === 422) {
            errors.value = error.response.data.errors || {}
        } else {
            errors.value = {}
        }
    }

    function clearErrors() {
        errors.value = {}
    }

    function getError(field) {
        return errors.value[field]?.[0] || ''
    }

    return {
        errors,
        setErrorsFromResponse,
        clearErrors,
        getError
    }
}
