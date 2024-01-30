import { Variant } from "@mui/material/styles/createTypography";

export interface Product {
    id: number;
    name: string;
    status: string;
    description: string;
    reservation_id: number;
    variants: Variant[];
}
