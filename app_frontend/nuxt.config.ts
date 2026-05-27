export default defineNuxtConfig({
    compatibilityDate: '2025-07-15',
    devtools: {enabled: true},
    typescript: {
        typeCheck: true,
    },
    runtimeConfig: {
        // Dev : Nuxt utilisera cette URL par défaut
        // Prod : Nuxt utilisera la varaible d'environnement injecté par Docker

        public: {
            apiBaseUrl: 'http://localhost:8000/api'
        }
    },
    vite: {
        server: {
            watch: {
                usePolling: true,
                interval: 100 // Vérifie toutes les 100ms
            }
        }
    }
})
