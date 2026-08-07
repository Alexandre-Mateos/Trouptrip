import type {IInvitation} from "~/interfaces/participation/i-invitation";

export interface IInvitationList{
    member: IInvitation[];
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}