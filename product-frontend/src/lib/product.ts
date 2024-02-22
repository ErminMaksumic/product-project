import { Variant } from "./variant";

interface Link {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
}

interface MetaLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Meta {
    current_page: number;
    from: number;
    last_page: number;
    links: MetaLink[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface Product {
    id?: number;
    name: string;
    description?: string;
    validFrom?: string;
    validTo?: string;
    status: string;
    activatedBy: string | null;
    variants: Variant[];
}

export interface ApiProductResponse {
    data: Product[];
    links: Link;
    meta: Meta;
}

export interface Batch {
    batch_id: string;
}
