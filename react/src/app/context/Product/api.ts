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

export async function upload(file:File) {
    const chunkSize = 1024 * 1024 * 50; // 50 MB chunks (adjust as needed)
    let start = 0;
    let end = Math.min(chunkSize, file.size);

     // Add the CSV header row
    const headerRow = 'name,description,product_type_id,created_at,updated_at,validFrom,validTo,status\n';

    try {
        while (start < file.size) {
            const chunk = file.slice(start, end);

            // Read the chunk as text
            const chunkText = await chunk.text();

            // Calculate the position of the last line break within the chunk
            const lastLineBreakIndex = chunkText.lastIndexOf('\n');

            // Adjust the end position to ensure the last line break is included in the chunk
            if (lastLineBreakIndex !== -1) {
                end = start + lastLineBreakIndex + 1; // Include the line break
            } else {
                // If no line break is found, set end to the end of the chunk
                end = start + chunk.size;
            }

            // Create a new Blob with header row + chunk data
            const blobData = headerRow + chunkText.substring(0, end - start); // Include only the portion up to the adjusted end position
            
            const blob = new Blob([blobData], { type: 'text/csv' });

            // Convert chunk to ArrayBuffer
            const arrayBuffer = await new Promise<ArrayBuffer>((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result as ArrayBuffer); // Type assertion
                reader.onerror = reject;
                reader.readAsArrayBuffer(blob);
            });

            // Create Uint8Array from ArrayBuffer
            const uint8Array = new Uint8Array(arrayBuffer);

            // Convert Uint8Array to regular array
            const dataArray = Array.from(uint8Array);

            // Send chunk as application/octet-stream
            await axios.post(
                `${process.env.NEXT_PUBLIC_URL}/api/upload`,
                new Uint8Array(dataArray), 
                {
                    headers: {
                        'Content-Type': 'application/octet-stream',
                        'Content-Disposition': `attachment; filename="${file.name}"`,
                    },
                }
            );

            start = end;
            end = Math.min(start + chunkSize, file.size);
        }

        console.log('Upload complete');
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
