"use client";

import { CustomDataGrid } from "@/components/CustomDataGrid";
import { Product } from "@/lib/product";
import { columns, columnsWithEdit } from "@/lib/productColumns";
import { useEffect, useState } from "react";
import { useProductApi } from "../context/Product/ProductContext";
import debounce from "lodash.debounce";
import { Table } from "@mui/material";
import TableComponent from "@/components/Table/TableComponent";

export default function Home() {
    const { getProducts } = useProductApi();
    const [product, setProduct] = useState<Product[]>();
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [filters, setFilters] = useState({
        name: "",
        priceGT: "",
        priceLT: "",
        validFrom: "",
        validTo: "",
    });

    const [query, setQuery] = useState<string | undefined>("");
    const [paramValues, setParamValues] = useState<Record<string, string>>({});

    const debouncedFetchData = debounce(async () => {
        try {
            const response = await getProducts(false, 1, query);
            console.log(response.data);
            setProduct(response.data);
            setLastPage(response.meta.last_page);
        } catch (error) {
            console.error("Error fetching products:", error);
        }
    }, 500);

    useEffect(() => {
        debouncedFetchData();

        return () => debouncedFetchData.cancel();
    }, [query]);

    const handlePageChange = async (newPage: number) => {
        try {
            const response = await getProducts(false, newPage, query);
            setProduct(response.data);
            setCurrentPage(newPage);
        } catch (error) {
            console.error("Error fetching products:", error);
        }
    };

    return (
        <div style={{ padding: "5%" }}>
            {/* <CustomDataGrid
                params={product}
                columns={columns}
                columnsWithEdit={columnsWithEdit}
                currentPage={currentPage}
                lastPage={lastPage}
                onPageChange={handlePageChange}
                filters={filters}
                onFilterChange={setFilters}
            /> */}
            <TableComponent
                products={product}
                currentPage={currentPage}
                totalPages={lastPage}
                handlePageChange={handlePageChange}
            />
        </div>
    );
}
