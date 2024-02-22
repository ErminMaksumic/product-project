"use client";

import React, { useEffect, useState } from "react";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    Typography,
} from "@mui/material";
import { orderStateButtons, Button as StateButton } from "@/lib/buttons";
import { Product } from "@/lib/product";
import ProductForm from "@/components/ProductForm";
import { columnsWithEdit, columns } from "@/lib/productColumns";
import { variantColumns, variantColumnsWithEdit } from "@/lib/variantColumns";
import { CustomDataGrid } from "@/components/CustomDataGrid";
import { ProductDetails } from "@/components/ProductDetails";
import VariantForm from "@/components/VariantForm";
import { useProductApi } from "@/app/context/Product/ProductContext";
import { Variant } from "@/lib/variant";

export default function Product({ params }: { params: { id: number } }) {
    const {
        getProductById,
        getAllowedActions,
        updateProduct,
        updateVariant,
        insertVariant,
    } = useProductApi();
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

        setProduct(productResponse);
        setVariants(variantsJson);
        setAllowedActions(allowedActionsResponse);
        setButtons(
            orderStateButtons.filter((x) =>
                allowedActionsResponse?.includes(x.state)
            )
        );
    }

    const updateState = async (path: string, object: any) => {
        await updateProduct(params.id, path, object);
        await fetchData();
    };

    const handleOpenModal = () => {
        setOpenModal(true);
    };

    const handleCloseModal = () => {
        setOpenModal(false);
    };

    const handleSubmitForm = async (formData: Product) => {
        await updateProduct(params.id, "", formData);
        await fetchData();
        handleCloseModal();
    };

    const handleEditVariant = (variant: Variant) => {
        setSelectedVariant(variant);
        setOpenVariantModal(true);
    };

    const handleSubmitVariantForm = async (formData: any) => {
        if (formData.id) {
            await updateVariant(formData.id, formData);
        } else {
            await insertVariant(formData);
        }
        setOpenVariantModal(false);
        fetchData();
    };

    const handleOpenVariantModalForInsert = () => {
        setSelectedVariant({
            name: "",
            price: 0,
            value: "",
            product_id: params.id,
        });
        setOpenVariantModal(true);
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
                {buttons?.map((button, index) => (
                    <Button
                        key={index}
                        variant="outlined"
                        sx={{ marginLeft: "80px", color: button.color }}
                        onClick={() => updateState(button.link, button.request)}
                    >
                        {button?.text}
                    </Button>
                ))}

                <Typography variant="h5" sx={{ mt: 3 }}>
                    Product Variants
                </Typography>
                {allowedActions?.includes("DraftToActive") && (
                    <Button
                        variant="outlined"
                        onClick={handleOpenVariantModalForInsert}
                    >
                        Add Variant
                    </Button>
                )}
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
