import Link from "next/link";

interface Column {
    field: string;
    headerName: string;
    width: number;
    renderCell?: (params: {
        row: Record<string, any>;
        value: any;
    }) => React.ReactNode;
}

export const columns: Column[] = [
    { field: "id", headerName: "ID", width: 200 },
    { field: "name", headerName: "Name", width: 400 },
    { field: "status", headerName: "Status", width: 400 },
];

export const columnsWithEdit: Column[] = [
    ...columns,
    {
        field: "actions",
        headerName: "Actions",
        width: 100,
        renderCell: (params) => (
            <Link href={`/products/${params.row.id}`}>Edit</Link>
        ),
    },
];
