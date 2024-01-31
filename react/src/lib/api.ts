import { Product } from "./product";
import { Variant } from "./variant";

export async function getProductById(id: number, includeVariant = false) {
    let url = `${process.env.NEXT_PUBLIC_URL}/api/product/${id}`;
    if (includeVariant) {
        url = `${process.env.NEXT_PUBLIC_URL}/api/product/${id}?includeVariants=true`;
    }
    return await fetch(url, {
        headers: {
            Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
        },
    });
}

export async function getAllowedActions(id: number) {
    return await fetch(
        `${process.env.NEXT_PUBLIC_URL}/api/product/${id}/allowedActions`,
        {
            headers: {
                Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
            },
        }
    );
}

export async function updateProduct(
    id: number,
    path: string,
    product: Product | null
) {
    return await fetch(
        `${process.env.NEXT_PUBLIC_URL}/api/product/${id}${path}`,
        {
            body: JSON.stringify(product),
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
            } as HeadersInit,
        }
    );
}

export async function updateVariant(id: number, variant: Variant) {
    return await fetch(`${process.env.NEXT_PUBLIC_URL}/api/variant/${id}`, {
        body: JSON.stringify(variant),
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
        } as HeadersInit,
    });
}

export async function getProducts(includeVariant: boolean) {
    let url = `${process.env.NEXT_PUBLIC_URL}/api/product`;
    if (includeVariant) {
        url = `${process.env.NEXT_PUBLIC_URL}/api/product?includeVariant=true`;
    }

    return await fetch(url, {
        headers: {
            Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
        },
    });
}
