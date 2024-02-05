"use client";
import React, { createContext, useContext, ReactNode } from "react";
import {
    getAllowedActions,
    getProductById,
    getProducts,
    updateProduct,
    updateVariant,
} from "./api";
import { Product } from "@/lib/product";
import { Variant } from "@/lib/variant";

interface ProductContextProps {
    getAllowedActions: (id: number) => Promise<string[]>;
    getProductById: (id: number, includeVariant: boolean) => Promise<Product>;
    getProducts: (includeVariant: boolean) => Promise<Product[]>;
    updateProduct: (
        id: number,
        path: string,
        product: Product | null
    ) => Promise<Product>;
    updateVariant: (id: number, variant: Variant) => Promise<Variant>;
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
