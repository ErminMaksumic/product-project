"use client";

import React, { useEffect, useState } from "react";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Typography,
} from "@mui/material";
import { CustomDataGrid } from "@/components/CustomDataGrid";
import { orderStateButtons, Button as StateButton } from "@/lib/buttons";
import { Product } from "@/lib/product";
import ProductForm from "@/components/ProductForm";
import { columnsWithEdit, columns } from "@/lib/productColumns";
import { variantColumns, variantColumnsWithEdit } from "@/lib/variantColumns";
import { ProductDetails } from "@/components/ProductDetails";
import VariantForm from "@/components/VariantForm";
import { useProductApi } from "@/app/context/Product/ProductContext";
import { Variant } from "@/lib/variant";

export default function Product({ params }: { params: { id: number } }) {
    const { getProductById, getAllowedActions, updateProduct, updateVariant } =
        useProductApi();
    const [selectedVariant, setSelectedVariant] = useState<Variant>();
    const [openVariantModal, setOpenVariantModal] = useState(false);
    const [product, setProduct] = useState<Product | null>(null);
    const [variants, setVariants] = useState<Variant[]>([]);
    const [allowedActions, setAllowedActions] = useState<string[]>([]);
    const [buttons, setButtons] = useState<StateButton[]>([]);
    const [openModal, setOpenModal] = useState(false);

    async function fetchData() {
        const productResponse = await getProductById(params.id, true);
        const allowedActionsResponse = await getAllowedActions(params.id);

        const variantsJson = productResponse.variants;
        console.log("allowedActionsJson", allowedActionsResponse);

        setProduct(productResponse);
        setVariants(variantsJson);
        setAllowedActions(allowedActionsResponse);
        setButtons(
            orderStateButtons.filter((x) =>
                allowedActionsResponse?.includes(x.state)
            )
        );
    }

    const updateState = async (path: string) => {
        await updateProduct(params.id, path, null);
        await fetchData();
    };

    const handleOpenModal = () => {
        setOpenModal(true);
    };

    const handleCloseModal = () => {
        setOpenModal(false);
    };

    const handleSubmitForm = async (formData: Product) => {
        console.log("hereee, handlesubmitform");
        await updateProduct(params.id, "", formData);
        await fetchData();
        handleCloseModal();
    };

    const handleEditVariant = (variant: Variant) => {
        setSelectedVariant(variant);
        setOpenVariantModal(true);
    };

    const handleSubmitVariantForm = async (formData: any) => {
        if (selectedVariant?.id)
            await updateVariant(selectedVariant?.id, formData);
        setOpenVariantModal(false);
        fetchData();
    };

    useEffect(() => {
        fetchData();
    }, []);

    return (
        <>
            <div style={{ padding: "5%" }}>
                <div style={{ marginBottom: "20px" }}>
                    <ProductDetails product={product}></ProductDetails>
                </div>

                <Button variant="outlined" onClick={handleOpenModal}>
                    Edit Product
                </Button>

                <Typography variant="h5" sx={{ mt: 3 }}>
                    Product Variants
                </Typography>
                <CustomDataGrid
                    params={variants}
                    columns={variantColumns}
                    columnsWithEdit={variantColumnsWithEdit}
                    handleEditVariant={handleEditVariant}
                ></CustomDataGrid>
            </div>
            <Dialog open={openModal} onClose={handleCloseModal}>
                <DialogTitle>Edit Product</DialogTitle>
                <DialogContent>
                    {product && (
                        <ProductForm
                            product={product}
                            onSubmit={handleSubmitForm}
                            onClose={() => setOpenModal(false)}
                        />
                    )}
                </DialogContent>
            </Dialog>
            <Dialog
                open={openVariantModal}
                onClose={() => setOpenVariantModal(false)}
            >
                <DialogTitle>Edit Variant</DialogTitle>
                <DialogContent>
                    {selectedVariant && (
                        <VariantForm
                            variant={selectedVariant}
                            onSubmit={handleSubmitVariantForm}
                            onClose={() => setOpenVariantModal(false)}
                        />
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}
