"use client";

import { CustomDataGrid } from "@/components/CustomDataGrid";
import { Product } from "@/lib/product";
import { columns, columnsWithEdit } from "@/lib/productColumns";
import { useEffect, useState } from "react";
import { useProductApi } from "../context/Product/ProductContext";

export default function Home() {
    const [product, setProduct] = useState<Product[]>();
    const { getProducts } = useProductApi();

    async function fetchData() {
        const product = await getProducts(false);
        setProduct(product);
    }

    useEffect(() => {
        fetchData();
    }, []);

    return (
        <div style={{ padding: "5%" }}>
            <CustomDataGrid
                params={product}
                columns={columns}
                columnsWithEdit={columnsWithEdit}
            ></CustomDataGrid>
        </div>
    );
}
