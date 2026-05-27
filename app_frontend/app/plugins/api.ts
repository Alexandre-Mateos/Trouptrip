export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig();
    const api = $fetch.create({
        baseURL: config.public.apiBaseUrl,
        onRequest({ options }) {
            const headers = new Headers(options.headers)
            headers.set('Content-Type', 'application/ld+json')
            headers.set('Accept', 'application/ld+json')
            options.headers = headers
        },
        async onResponseError({ response }) {
            if (response.status === 401) {
                // Le runWithContext évite les crashs de contexte Nuxt en asynchrone
                await nuxtApp.runWithContext(() => navigateTo('/login'))
            }
        }
    })

    return {
        provide: {
            api: api
        }
    }
})