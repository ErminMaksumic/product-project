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
    { field: "id", headerName: "ID", width: 70 },
    { field: "name", headerName: "Name", width: 400 },
    { field: "description", headerName: "Description", width: 400 },
    { field: "status", headerName: "Status", width: 400 },
];

export const columnsWithEdit: Column[] = [
    ...columns,
    {
        field: "actions",
        headerName: "Actions",
        width: 100,
        renderCell: (params) => (
            <Link href={`/homepage/${params.row.id}`}>Edit</Link>
        ),
    },
];
