"use client";

import { CustomDataGrid } from "@/app/components/CustomDataGrid";
import { getProducts } from "@/lib/api";
import { Product } from "@/lib/product";
import { useEffect, useState } from "react";

export default function Home() {
    const [product, setProduct] = useState<Product>();

    async function fetchData() {
        const product = await getProducts();
        const productJson = await product.json();
        setProduct(productJson.data);
    }

    useEffect(() => {
        fetchData();
    }, []);

    return (
        <div style={{ padding: "5%" }}>
            <CustomDataGrid params={product}></CustomDataGrid>
        </div>
    );
}
