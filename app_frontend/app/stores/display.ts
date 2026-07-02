export const useDisplayStore = defineStore('header', {
    state: () => ({
        TrouptripHome: {to: '/', label: 'Accueil'},
        Login: {to: '/login', label: 'Se connecter'},
        Register: {to: '/register', label: 'Créer un compte'},
        UserHome: {to: '/home', label: 'Mon espace'},
        Trips: {to: '/trips', label: 'Mes séjours'},
        isDesktop: null as boolean | null,
    }),
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
        }
    }
})