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

        return response.data.data;
    } catch (error) {
        console.error("Error getting allowed actions:", error);
        throw error;
    }
}

export async function updateProduct(
    id: number,
    path: string,
    product: Product | null
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

export async function getProducts(includeVariant: boolean) {
    try {
        let url = `${process.env.NEXT_PUBLIC_URL}/api/product`;
        if (includeVariant) {
            url = `${process.env.NEXT_PUBLIC_URL}/api/product?includeVariant=true`;
        }

        const response = await axios.get(url);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching products:", error);
        throw error;
    }
}
