import { Button } from "@mui/material";
import { Variant } from "./variant";

interface VariantColumnProps {
    handleEditVariant: (variant: Variant) => void;
}

interface VariantColumn {
    field: string;
    headerName: string;
    width: number;
    renderCell?: (
        params: {
            row: Record<string, any>;
            value: any;
        },
        props: VariantColumnProps
    ) => React.ReactNode;
}

export const variantColumns: VariantColumn[] = [
    { field: "id", headerName: "ID", width: 70 },
    { field: "name", headerName: "Name", width: 300 },
    { field: "price", headerName: "Price", width: 300 },
    { field: "value", headerName: "Value", width: 300 },
];

export const variantColumnsWithEdit = [
    ...variantColumns,
    {
        field: "actions",
        headerName: "Actions",
        width: 150,
        renderCell: (params, props) => (
            <Button
                variant="outlined"
                onClick={() => props.handleEditVariant(params.row)}
            >
                Edit
            </Button>
        ),
    },
];
