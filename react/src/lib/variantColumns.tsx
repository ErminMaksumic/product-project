import Link from "next/link";

interface VariantColumn {
    field: string;
    headerName: string;
    width: number;
    renderCell?: (params: {
        row: Record<string, any>;
        value: any;
    }) => React.ReactNode;
}

export const variantColumns: VariantColumn[] = [
    { field: "id", headerName: "ID", width: 70 },
    { field: "name", headerName: "Name", width: 400 },
    { field: "price", headerName: "Price", width: 400 },
    { field: "value", headerName: "Valu", width: 400 },
];

export const variantColumnsWithEdit: VariantColumn[] = [
    ...variantColumns,
    {
        field: "actions",
        headerName: "Actions",
        width: 100,
        renderCell: (params) => (
            <Link href={`/homepage/${params.row.id}`}>Test</Link>
        ),
    },
];
