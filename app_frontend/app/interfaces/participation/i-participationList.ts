import type {IParticipation} from "~/interfaces/participation/i-participation";

export interface IParticipationList{
    member: IParticipation[];
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}