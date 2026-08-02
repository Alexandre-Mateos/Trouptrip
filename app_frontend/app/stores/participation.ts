import type {IMapParticipation} from "~/interfaces/participation/i-mapParticipation";
import type {IParticipantList} from "~/interfaces/i-participantList";

export const useParticipationStore = defineStore('participation', {
    state: () => ({
        // Map<participationId, IMapParticipation>
        participations: new Map<number, IMapParticipation>(),
        loading: false
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
    }
});