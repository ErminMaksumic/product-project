"use client";

import { CustomDataGrid } from "@/components/CustomDataGrid";
import { getProducts } from "@/lib/api";
import { Product } from "@/lib/product";
import { columns, columnsWithEdit } from "@/lib/productColumns";
import { useEffect, useState } from "react";
import Loader from "../../components/Loader/Loader";

export default function Home() {
    const [product, setProduct] = useState<Product>();
    const [loading, setLoading] = useState(false);

    async function fetchData() {
        try {
            setLoading(true);
            const product = await getProducts(false);
            const productJson = await product.json();
            setProduct(productJson.data);
        } catch(error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        fetchData();
    }, []);

    return (
        <div>
            {
                loading ?
                <Loader /> :
                <CustomDataGrid
                    params={product}
                    columns={columns}
                    columnsWithEdit={columnsWithEdit}
                ></CustomDataGrid>
            }
        </div>
    );
}
