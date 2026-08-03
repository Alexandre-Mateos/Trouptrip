export const useDisplayStore = defineStore('header', {
    state: () => ({
        TrouptripHome: {to: '/', label: 'Accueil'},
        Login: {to: '/login', label: 'Se connecter'},
        Register: {to: '/register', label: 'Créer un compte'},
        UserHome: {to: '/home', label: 'Mon espace'},
        Trips: {to: '/trips', label: 'Mes séjours'},
        isDesktop: null as boolean | null,
        tripNavTab:[
            {key: 'my-trip', label: 'Mon séjour'}
        ],
        activeTripNavTab: 'my-trip'
    }),
    getters: {
        isActiveTab(state) {
            return (tabKey: string): boolean => {
                return state.activeTripNavTab === tabKey;
            };
        }
    },
    actions: {
        constructHeader(){
            const userStore = useUserStore();
            const tripsStore = useTripsStore();

            if (userStore.user){
                if(this.isDesktop && tripsStore.trips.size > 0){
                    const tripId = tripsStore.getFirstTripId
                    this.Trips.to = `/trips/${tripId}`;
                }

                return [this.UserHome, this.Trips];
            }

            return [this.TrouptripHome, this.Login, this.Register];
        },
        activateTripTab(activeTab: string){
            this.activeTripNavTab = activeTab;
        }
    }
})