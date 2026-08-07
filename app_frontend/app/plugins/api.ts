export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig();

    const api = $fetch.create({
        baseURL: config.public.apiBaseUrl,
        credentials: 'include',

        onRequest({ options }) {
            const headers = new Headers(options.headers);

            if (!headers.has('Content-Type')) {
                headers.set('Content-Type', 'application/ld+json');
            }
            headers.set('Accept', 'application/ld+json');

            options.headers = headers;
        },

        async onResponseError({ response }) {
            if (response.status === 401) {
                await nuxtApp.runWithContext(() => navigateTo('/login'));
            }
        }
    });

    return {
        provide: {
            api: api
        }
    };
});