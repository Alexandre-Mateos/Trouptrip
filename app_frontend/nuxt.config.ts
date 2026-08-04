export default defineNuxtConfig({
    compatibilityDate: '2025-07-15',
    devtools: {enabled: true},
    ssr: false,

    typescript: {
        typeCheck: true,
    },

    runtimeConfig: {
        // Nuxt utilisera la variable d'environnement injectée par Docker
        public: {
            apiBaseUrl: ''
        }
    },

    vite: {
        server: {
            watch: {
                usePolling: true,
                interval: 100, // Vérifie toutes les 100ms

                // AJOUTS POUR STABILISER WSL2 & DOCKER :
                // Laisse 100ms de répit pour s'assurer que VS Code (Windows) a fini
                // d'écrire le fichier dans WSL avant que Vite ne tente de compiler.
                awaitWriteFinish: {
                    stabilityThreshold: 100,
                    pollInterval: 100
                },
                // Évite que le watcher ne scanne les fichiers générés par Nuxt,
                // ce qui peut provoquer des boucles de rafraîchissement et saturer l'IPC.
                ignored: [
                    '**/.nuxt/**',
                    '**/.output/**',
                    '**/node_modules/**',
                    '**/.git/**'
                ]
            }
        }
    },
    modules: ['@pinia/nuxt', '@nuxt/icon', '@nuxt/ui'],
    ui: {
        theme: {
            colors: ['primary', 'secondary', 'error', 'trouptrip-accent', 'trouptrip-secondary', 'trouptrip-error', 'trouptrip-neutral']
        }
    },
    css: ['~/assets/styles/main.css'],
})