import type {IMapParticipation} from "~/interfaces/participation/i-mapParticipation";
import type {IParticipantList} from "~/interfaces/i-participantList";
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IParticipationList} from "~/interfaces/participation/i-participationList";
import type {IParticipation} from "~/interfaces/participation/i-participation";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import {participationStatus} from "~/utils/participationStatus";
import type {IInvitation} from "~/interfaces/participation/i-invitation";
import type {IInvitationList} from "~/interfaces/participation/i-invitationList";

export const useParticipationStore = defineStore('participation', {
    state: () => ({
        // Map<participationId, IMapParticipation>
        participations: new Map<number, IMapParticipation>(),
        invitations: new Map<number, IInvitation>(),
        fetching: false,
    }),
    getters: {
        getParticipantsGroupedByStatusByTripId: (state) => {
            return (tripId: number): Record<string, IParticipantList[]> => {
                const trip = useTripsStore().getTripById(tripId);

                const groupedParticipant: Record<string, IParticipantList[]> = {
                    ACCEPTED: [],
                    PENDING: [],
                    DECLINED: [],
                    LEFT: [],
                    EXCLUDED: []
                };

                if (!trip || !trip.isDetail || !trip.participationIds) {
                    return groupedParticipant;
                }

                const userStore = useUserStore();
                const currentUserId = userStore.user?.id;

                trip.participationIds.forEach((participationId) => {
                    const participation = state.participations.get(participationId);
                    if (!participation) {
                        return;
                    }

                    if (currentUserId !== participation.participantId) {
                        const user = userStore.tripUsers.get(participation.participantId);

                        if (user) {
                            const participant: IParticipantList = {
                                participationId: participation.id,
                                firstname: user.firstname,
                                lastname: user.lastname,
                                status: participation.status,
                            };

                            const targetGroup = groupedParticipant[participation.status];
                            if (targetGroup) {
                                targetGroup.push(participant);
                            }
                        }
                    }
                });
                return groupedParticipant;
            };
        },
        getParticipationByTripAndUser: (state) => {
            return (tripId: number): IMapParticipation | undefined => {
                const trip = useTripsStore().getTripById(tripId);

                if (!trip?.isDetail || !trip.participationIds) {
                    return undefined;
                }

                const currentUserId = useUserStore().user?.id;
                if (!currentUserId) return undefined;

                for (const participationId of trip.participationIds) {
                    const participation = state.participations.get(participationId);

                    if (participation && participation.participantId === currentUserId) {
                        return participation;
                    }
                }
                return undefined;
            };
        },
        invitationList: (state) => Array.from(state.invitations.values()),
    },
    actions: {
        async fetchParticipations(tripId: number) {
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
        }) {

            if (this.fetching) {
                return;
            }

            this.fetching = true;
            const {$api} = useNuxtApp();
            const url = apiEndpoints.participations;

            const body = {...payload, trip: trip["@id"]};

            try {
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
                });

                if (!trip.participationIds.includes(response.id)) {
                    trip.participationIds.push(response.id);
                }
            } finally {
                this.fetching = false;
            }
        },
        async updateParticipationStatus(participationId: number, body: { status: string }) {

            if (this.fetching) {
                return;
            }

            this.fetching = true;
            const {$api} = useNuxtApp();
            const url = `${apiEndpoints.participations}/${participationId}`;

            try {
                const response = await $api<IParticipation>(url, {
                    method: 'PATCH',
                    body
                });

                this.participations.set(response.id, {
                    "@id": response["@id"],
                    "@type": response["@type"],
                    id: response.id,
                    status: response.status,
                    participantId: response.participant.id
                });

            } finally {
                this.fetching = false;
            }
        },
        async excludeParticipant(participationId: number) {
            await this.updateParticipationStatus(participationId, {
                status: participationStatus.excluded
            });
        },
        async exitTrip(participationId: number) {
            await this.updateParticipationStatus(participationId, {
                status: participationStatus.left
            });
        },
        async fetchInvitations() {

            if (this.fetching) {
                return;
            }

            this.fetching = true;
            const {$api} = useNuxtApp();

            try {
                const invitationsCollection = await $api<IInvitationList>(apiEndpoints.invitations);
                invitationsCollection.member.forEach((invitation) => {
                    this.invitations.set(invitation.id, invitation);
                });
            } finally {
                this.fetching = false;
            }
        },
        async acceptInvitation(participationId: number){
            await this.updateParticipationStatus(participationId, {
                status: participationStatus.accepted
            });
        },
        async declineInvitation(participationId: number){
            await this.updateParticipationStatus(participationId, {
                status: participationStatus.declined
            });
        },
        removeInvitation(participationId: number){
            this.invitations.delete(participationId);
        },
        clearStore(){
            this.participations.clear();
            this.invitations.clear();
            this.fetching = false;
        }
    }
});