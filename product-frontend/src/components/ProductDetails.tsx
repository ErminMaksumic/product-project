import { Product } from "@/lib/product";
import { Typography } from "@mui/material";

export function ProductDetails({ product }: { product: Product | null }) {
    return (
        <div>
            <Typography variant="h6">Product Information</Typography>
            {product && (
                <div>
                    <p>ID: {product.id}</p>
                    <p>Name: {product.name}</p>
                    <p>Status: {product.status}</p>
                    <p>Description: {product.description}</p>
                </div>
            )}
        </div>
    );
}
