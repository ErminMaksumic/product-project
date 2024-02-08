import { Product } from "@/lib/product";
import { Variant } from "@/lib/variant";
import axios from "axios";

const authToken = localStorage.getItem("bearerToken");
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
