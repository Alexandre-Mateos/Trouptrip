export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig();
    const api = $fetch.create({
        baseURL: config.public.apiBaseUrl,
        onRequest({ options }) {
            const headers = new Headers(options.headers);

            if (!headers.has('Content-Type')) {
                headers.set('Content-Type', 'application/ld+json');
            }
            headers.set('Accept', 'application/ld+json');
            options.headers = headers;

            // inject le token stocké en session à chaque requete
            const token = sessionStorage.getItem('token');
            if(token){
                options.headers.set('Authorization', `Bearer ${token}`);
            }

        },
        onResponse({response}){
            if(response.url.includes('login_check')){
                const token = response._data?.token;
                if(token){
                    sessionStorage.setItem('token', token);
                }
            }
        },
        async onResponseError({ response }) {
            if (response.status === 401) {
                sessionStorage.removeItem('token');
                // Le runWithContext évite les crashs de contexte Nuxt en asynchrone
                await nuxtApp.runWithContext(() => navigateTo('/login'));
            }
        }
    })

    return {
        provide: {
            api: api
        }
    }
})