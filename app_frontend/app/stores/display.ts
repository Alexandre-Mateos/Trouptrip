export const useDisplayStore = defineStore('header', {
    state: () => ({
        TrouptripHome: { to: '/', label: 'Accueil' },
        Login: { to: '/login', label: 'Se connecter' },
        Register: { to: '/register', label: 'Créer un compte' },
        UserHome: { to: '/home', label: 'Mon espace' },
        Trips: { to: '/trips', label: 'Mes séjours' },
        Invitations: { to: '/invitations', label: 'Mes invitations' },
        isDesktop: null as boolean | null,
        tripNavTab: [
            { key: 'my-trip', label: 'Mon séjour' }
        ],
        activeTripNavTab: 'my-trip'
    }),
    getters: {
        isActiveTab(state) {
            return (tabKey: string): boolean => {
                return state.activeTripNavTab === tabKey;
            };
        },
        headerNav(state) {
            const userStore = useUserStore();
            const tripsStore = useTripsStore();

            if (userStore.user) {
                let tripsItem = state.Trips;

                if (state.isDesktop && tripsStore.trips.size > 0) {
                    const tripId = tripsStore.getFirstTripId;
                    tripsItem = {
                        ...state.Trips,
                        to: `/trips/${tripId}`
                    };
                }

                return [state.UserHome, tripsItem, state.Invitations];
            }

            return [state.TrouptripHome, state.Login, state.Register];
        }
    },
    actions: {
        activateTripTab(activeTab: string) {
            this.activeTripNavTab = activeTab;
        }
    }
})