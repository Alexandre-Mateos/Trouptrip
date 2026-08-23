import type {IGroupItem} from "~/interfaces/groupItem/i-groupItem";

export interface IGroupItemList{
    member: IGroupItem[];
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}