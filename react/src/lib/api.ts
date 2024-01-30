export async function getProductById(id: number) {
    return await fetch(`${process.env.NEXT_PUBLIC_URL}/product/${id}`, {
        headers: {
            Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
        },
    });
}

export async function getAllowedActions(id: number) {
    console.log(`${process.env.NEXT_PUBLIC_URL}/product/${id}/allowedActions`);
    return await fetch(
        `${process.env.NEXT_PUBLIC_URL}/product/${id}/allowedActions`,
        {
            headers: {
                Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
            },
        }
    );
}

export async function updateProduct(id: number, path: string) {
    return await fetch(`${process.env.NEXT_PUBLIC_URL}/product/${id}${path}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${process.env.NEXT_PUBLIC_AUTH}`,
        } as HeadersInit,
    });
}

export async function getProducts() {
    return await fetch(`${process.env.NEXT_PUBLIC_URL}/product`);
}
