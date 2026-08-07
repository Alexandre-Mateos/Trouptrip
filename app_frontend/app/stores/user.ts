import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IUserMe} from "~/interfaces/i-userMe";
import type {ITripUser} from "~/interfaces/user/i-tripUser";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null as IUserMe | null,
        userFetching: false,
        tripUsers: new Map<number, ITripUser>
    }),
    actions: {
        async fetchUser() {
            if (this.user || this.userFetching) {
                return;
            }
            const { $api } = useNuxtApp();
            this.userFetching = true;

            try {
                this.user = await $api<IUserMe>(apiEndpoints.userMe);
            } catch (error: any) {
                this.user = null;
            } finally {
                this.userFetching = false;
            }
        },
        async logout(){
            const { $api } = useNuxtApp();
            await $api(apiEndpoints.logout);

            // Vider tous les stores pour éviter de conserver des infos utilisateur
            this.user = null;
            useTripsStore().clearStore();
            usePersonalItemsStore().clearStore();
            useParticipationStore().clearStore();
            useGroupItemsStore().clearStore();
            useAssignmentsStore().clearStore();

            // redirection
            await navigateTo('/login');
        }
    }
})