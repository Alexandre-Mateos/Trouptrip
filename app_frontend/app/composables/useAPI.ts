export const useAPI = createUseFetch((callerOptions) => {
    return {
        $fetch: useNuxtApp().$api as any,
        ...callerOptions
    }
})