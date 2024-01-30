"use client";
import * as React from "react";
import { columnsWithEdit, columns } from "@/lib/columns";
import { DataGrid } from "@mui/x-data-grid";
import { v4 as uuidv4 } from "uuid";

export function CustomDataGrid({ params }: any) {
    console.log("Array.isArray(params)", Array.isArray(params));
    console.log("params", params);
    return (
        <div style={{ height: "100%", width: "100%", background: "white" }}>
            {params && (
                <DataGrid
                    rows={Array.isArray(params) ? params : [params]}
                    columns={Array.isArray(params) ? columnsWithEdit : columns}
                    pageSizeOptions={[5, 10]}
                    getRowId={() => uuidv4()}
                />
            )}
        </div>
    );
}
