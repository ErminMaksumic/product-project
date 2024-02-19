import { Product } from "@/lib/product";
import { Variant } from "@/lib/variant";
import axios from "axios";

const authToken = localStorage.getItem("accessToken");
axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`;

export async function getProductById(id: number, includeVariant = false) {
    let url = `${process.env.NEXT_PUBLIC_URL}/api/product/${id}`;

    if (includeVariant) {
        url = `${process.env.NEXT_PUBLIC_URL}/api/product/${id}?includeVariants=true`;
    }

    try {
        const response = await axios.get(url);

        return response.data.data;
    } catch (error) {
        console.error("Error fetching product by id:", error);
        throw error;
    }
}

export async function getAllowedActions(id: number) {
    try {
        const response = await axios.get(
            `${process.env.NEXT_PUBLIC_URL}/api/product/${id}/allowedActions`
        );

        return response.data;
    } catch (error) {
        console.error("Error getting allowed actions:", error);
        throw error;
    }
}

export async function updateProduct(
    id: number,
    path: string,
    product: Product | null | {}
) {
    try {
        const response = await axios.put(
            `${process.env.NEXT_PUBLIC_URL}/api/product/${id}${path}`,
            product,
            {
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );

        return response.data.data;
    } catch (error) {
        console.error("Error updating product:", error);
        throw error;
    }
}

export async function updateVariant(id: number, variant: Variant) {
    try {
        const response = await axios.put(
            `${process.env.NEXT_PUBLIC_URL}/api/variant/${id}`,
            variant,
            {
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );

        return response.data.data;
    } catch (error) {
        console.error("Error updating variant:", error);
        throw error;
    }
}

export async function getProducts(
    includeVariant: boolean = false,
    page: number = 1,
    query: string | undefined
) {
    try {
        const url = `${process.env.NEXT_PUBLIC_URL}/api/product?includeVariant=${includeVariant}}&page=${page}&${query}`;

        const response = await axios.get(url);
        return response.data;
    } catch (error) {
        console.error("Error fetching products:", error);
        throw error;
    }
}

export async function generateReportForOneProduct(
    id: number,
    body: { formats: string[] }
) {
    try {
        const response = await axios.post(
            `${process.env.NEXT_PUBLIC_URL}/api/product/${id}/generateReport`,
            {
                formats: body.formats.map((format) => format.toLowerCase()),
            }
        );
        download(response);

        return response.data;
    } catch (error) {
        console.error("Error inserting variant:", error);
        throw error;
    }
}

export async function generateReportForExpensiveProducts(body: {
    formats: string[];
}) {
    try {
        const response = await axios.post(
            `${process.env.NEXT_PUBLIC_URL}/api/product/generateReport`,
            {
                formats: body.formats.map((format) => format.toLowerCase()),
            }
        );
        download(response);

        return response.data;
    } catch (error) {
        console.error("Error inserting variant:", error);
        throw error;
    }
}

export async function generateReportForProductStatesGraph(body: {
    formats: string[];
}) {
    try {
        const response = await axios.post(
            `${process.env.NEXT_PUBLIC_URL}/api/product/generateReportChart`,
            {
                formats: body.formats.map((format) => format.toLowerCase()),
            }
        );

        download(response);

        return response.data;
    } catch (error) {
        console.error("Error inserting variant:", error);
        throw error;
    }
}

export async function insertVariant(variantData: Variant) {
    try {
        const response = await axios.post(
            `${process.env.NEXT_PUBLIC_URL}/api/product/variant`,
            variantData,
            {
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );
        return response.data;
    } catch (error) {
        console.error("Error inserting variant:", error);
        throw error;
    }
}

async function download(response: any) {
    const { filePaths } = response.data;

    for (let i = 0; i < filePaths.length; i++) {
        const filePath = filePaths[i];
        const url = `${
            process.env.NEXT_PUBLIC_URL
        }/api/download?filePath=${encodeURIComponent(filePath)}`;
        const isPopupsBlocked = window.open(url, "_blank");
        if (!isPopupsBlocked) {
            alert("Please enable pop-ups to download multiple files.");
            return;
        }
    }
}



async function processChunk(
    start: number,
    end: number,
    rows: string[],
    file: File
) {
  try {
        const chunkRows = rows.slice(start, end);
        const cleanedRows = chunkRows.map(row => row.replace(/\r\n|\n|\r/g, "\\n"));
        const blobData = cleanedRows.join("\n");
        const blob = new Blob([blobData], { type: "application/octet-stream" });

        await axios.post(
            `${process.env.NEXT_PUBLIC_URL}/api/upload`,
            blob,
            {
                headers: {
                    "Content-Type": "application/octet-stream",
                    "Content-Disposition": `attachment; filename="${file.name}"`,
                },
            }
        );
    } catch (error) {
        console.error("Error processing chunk:", error);
        throw error; 
    }
}


export async function upload(file: File) {
    const rowsPerChunk = 200000;
    const fileText = await file.text();
    const rows = fileText.split("\n");

    try {
        for (let start = 0; start < rows.length; start += rowsPerChunk) {
            const end = Math.min(start + rowsPerChunk, rows.length);
            await processChunk(start, end, rows, file);
        }

        console.log("Upload complete");
    } catch (error) {
        console.error("Error uploading file:", error);
        throw error;
    }
}

export async function fetchBatchProgress(batchId: string) {
    try {
        const response = await axios.get(
            `${process.env.NEXT_PUBLIC_URL}/api/batch/progress/${batchId}`
        );
        return response.data.progress;
    } catch (error) {
        console.error("Error fetching batch progress:", error);
        return 0;
    }
}
