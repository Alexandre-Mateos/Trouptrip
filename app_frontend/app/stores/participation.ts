import type {IMapParticipation} from "~/interfaces/participation/i-mapParticipation";
import type {IParticipantList} from "~/interfaces/i-participantList";
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IParticipationList} from "~/interfaces/participation/i-participationList";
import type {IParticipation} from "~/interfaces/participation/i-participation";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";

export const useParticipationStore = defineStore('participation', {
    state: () => ({
        // Map<participationId, IMapParticipation>
        participations: new Map<number, IMapParticipation>(),
        fetching: false,
    }),
    getters: {
        getParticipantListByTripId: (state) => {
            return (tripId: number): IParticipantList[] => {

                const trip = useTripsStore().getTripById(tripId);

                if (!trip || !trip.isDetail || !trip.participationIds) {
                    return [];
                }

                const userStore = useUserStore();
                const currentUserId = userStore.user?.id;

                const participantList: IParticipantList[] = [];

                trip.participationIds.forEach((participationId) => {
                    const participation = state.participations.get(participationId);
                    if (!participation) return;

                    if (currentUserId !== participation.participantId) {
                        const user = userStore.tripUsers.get(participation.participantId);

                        if (user) {
                            participantList.push({
                                firstname: user.firstname,
                                lastname: user.lastname,
                                status: participation.status,
                            });
                        }
                    }
                });

                return participantList;
            };
        }
    },
    actions: {
        async fetchParticipations(tripId: number){
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try {
                const participationCollection = await $api<IParticipationList>(apiEndpoints.participationCollection(tripId));

                const userStore = useUserStore();

                participationCollection.member.forEach((participation) => {

                    this.participations.set(
                        participation.id,
                        {
                            "@id": participation["@id"],
                            "@type": participation["@type"],
                            id: participation.id,
                            status: participation.status,
                            participantId: participation.participant.id
                        }
                    )

                    userStore.tripUsers.set(
                        participation.participant.id,
                        participation.participant
                    );
                });

            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        },
        async inviteUser(trip: IMapTripDetails, payload: {
            email: string
        }){

            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            const url = apiEndpoints.participations;

            const body = {...payload, trip: trip["@id"]};

            try{
                const response = await $api<IParticipation>(url, {
                    method: 'POST',
                    body: body
                });

                this.participations.set(response.id, {
                    "@id": response["@id"],
                    "@type": response["@type"],
                    id: response.id,
                    status: response.status,
                    participantId: response.participant.id
                })

                if(!trip.participationIds.includes(response.id)){
                    trip.participationIds.push(response.id);
                }

            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        }
    }
});