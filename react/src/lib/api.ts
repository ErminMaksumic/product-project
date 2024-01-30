import { Product } from "./product";

export async function getProductById(id: number) {
    return await fetch(`${process.env.NEXT_PUBLIC_URL}/api/product/${id}`, {
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
    product: Product |null
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

export async function getProducts() {
    return await fetch(`${process.env.NEXT_PUBLIC_URL}/api/product`);
}
