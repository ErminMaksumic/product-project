"use client";
import React, { createContext, useContext, ReactNode } from "react";
import {
    getAllowedActions,
    getProductById,
    getProducts,
    updateProduct,
    updateVariant,
    insertVariant,
    generateReportForExpensiveProducts,
    generateReportForOneProduct,
    generateReportForProductStatesGraph,
    upload,
    fetchBatchProgress,
} from "./api";
import { Product, ApiProductResponse, Batch } from "@/lib/product";
import { Variant } from "@/lib/variant";

interface ProductContextProps {
    getAllowedActions: (id: number) => Promise<string[]>;
    getProductById: (id: number, includeVariant: boolean) => Promise<Product>;
    getProducts: (
        includeVariant: boolean,
        page: number,
        query: string | undefined
    ) => Promise<ApiProductResponse>;
    updateProduct: (
        id: number,
        path: string,
        product: Product | null
    ) => Promise<Product>;
    updateVariant: (id: number, variant: Variant) => Promise<Variant>;
    insertVariant: (variantData: Variant) => Promise<Variant>;
    generateReportForOneProduct: (
        id: number,
        body: { formats: string[] }
    ) => Promise<any>;
    generateReportForExpensiveProducts: (body: {
        formats: string[];
    }) => Promise<any>;
    generateReportForProductStatesGraph: (body: {
        formats: string[];
    }) => Promise<any>;
    upload(file: File, progressCallback: (progress: number) => void): Promise<any>;
    fetchBatchProgress(batchId: string): Promise<number>;
}

const ProductContext = createContext<ProductContextProps | undefined>(
    undefined
);

export const ProductProvider: React.FC<{ children: ReactNode }> = ({
    children,
}) => {
    return (
        <ProductContext.Provider
            value={{
                getAllowedActions,
                getProductById,
                getProducts,
                updateProduct,
                updateVariant,
                insertVariant,
                generateReportForExpensiveProducts,
                generateReportForOneProduct,
                generateReportForProductStatesGraph,
                upload,
                fetchBatchProgress,
            }}
        >
            {children}
        </ProductContext.Provider>
    );
};

export const useProductApi = () => {
    const context = useContext(ProductContext);
    if (!context) {
        throw new Error("useProductApi must be used within an ProductProvider");
    }
    return context;
};
