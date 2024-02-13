"use client";

import { CustomDataGrid } from "@/components/CustomDataGrid";
import { Product } from "@/lib/product";
import { columns, columnsWithEdit } from "@/lib/productColumns";
import { useEffect, useState } from "react";
import { useProductApi } from "../context/Product/ProductContext";
import debounce from "lodash.debounce";
import { Table } from "@mui/material";
import TableComponent from "@/components/Table/TableComponent";
import styles from "./page.module.scss";
import ReportComponent from "@/components/Report/ReportComponent";
import FileUploader from "@/components/FileUploader/FileUploader";

export default function Home() {
    const { getProducts, upload } = useProductApi();
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

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setCurrentPage(1);
        const { name, value } = e.target;

        setFilters({
            ...filters,
            [name]: value,
        });

        setParamValues((prevValues) => {
            const updatedValues = {
                ...prevValues,
                [name]: value,
            };

            const newQuery = Object.keys(updatedValues)
                .map(
                    (param) =>
                        `${param}=${encodeURIComponent(updatedValues[param])}`
                )
                .join("&");

            setQuery(newQuery);

            return updatedValues;
        });
    };

    const handleResetFilters = () => {
        setFilters({
            name: "",
            priceGT: "",
            priceLT: "",
            validFrom: "",
            validTo: "",
        });
        setParamValues({});
        setQuery("");
        setCurrentPage(1);
    };

    return (
        <div style={{ padding: "5%" }}>
            <div className={styles.filterInputs}>
                <div className={styles.twinInputs}>
                    <div className={styles.filterInput}>
                        <label htmlFor="priceGT" className={styles.label}>
                            Price Greater Than:
                        </label>
                        <input
                            type="number"
                            name="priceGT"
                            placeholder="Price Greater Than"
                            value={filters.priceGT}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                    </div>
                    <div className={styles.filterInput}>
                        <label htmlFor="priceLTE" className={styles.label}>
                            Price Less Than or Equal:
                        </label>
                        <input
                            type="number"
                            name="priceLT"
                            placeholder="Price Less Than"
                            value={filters.priceLT}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                    </div>
                </div>
                <div className={styles.twinInputs}>
                    <div className={styles.filterInput}>
                        <label htmlFor="valid_from" className={styles.label}>
                            Valid From:
                        </label>
                        <input
                            type="date"
                            name="validFrom"
                            placeholder="Valid From"
                            value={filters.validFrom}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                    </div>
                    <div className={styles.filterInput}>
                        <label htmlFor="valid_to" className={styles.label}>
                            Valid To:
                        </label>
                        <input
                            type="date"
                            name="validTo"
                            placeholder="Valid To"
                            value={filters.validTo}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                    </div>
                </div>
                <div className={styles.twinInputs}>
                    <div className={styles.filterInput}>
                        <label htmlFor="name" className={styles.label}>
                            Name:
                        </label>
                        <input
                            type="text"
                            name="name"
                            placeholder="Name"
                            value={filters.name}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                    </div>

                    <button
                        onClick={handleResetFilters}
                        className={styles.resetButton}
                    >
                        Reset All Filters
                    </button>
                </div>
            </div>
            <hr />
            <ReportComponent />
            <hr />
            <FileUploader
                title="Products File Uploader"
                onFileUpload={upload}
            />
            <TableComponent
                products={product}
                currentPage={currentPage}
                totalPages={lastPage}
                handlePageChange={handlePageChange}
            />
        </div>
    );
}
