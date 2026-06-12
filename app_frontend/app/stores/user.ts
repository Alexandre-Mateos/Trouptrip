import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IUserMe} from "~/interfaces/i-userMe";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null as IUserMe | null,
        fetching: false
    }),
    actions: {
        async fetchUser() {
            if (this.user || this.fetching) {
                return;
            }
            const { $api } = useNuxtApp();
            this.fetching = true;

            try {
                this.user = await $api<IUserMe>(apiEndpoints.userMe);
            } catch (error: any) {
                this.user = null;
            } finally {
                this.fetching = false;
            }
        },
    }
})