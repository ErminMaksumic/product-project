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
import { CustomDataGrid } from "@/app/components/CustomDataGrid";
import { orderStateButtons, Button as StateButton } from "@/lib/buttons";
import { getAllowedActions, getProductById, updateProduct } from "@/lib/api";
import { Product } from "@/lib/product";
import ProductForm from "@/app/components/ProductForm";
import { Variant } from "@mui/material/styles/createTypography";
import { columnsWithEdit, columns } from "@/lib/productColumns";
import { variantColumns, variantColumnsWithEdit } from "@/lib/variantColumns";
import { ProductDetails } from "@/app/components/ProductDetails";

export default function Product({ params }: { params: { id: number } }) {
    const [product, setProduct] = useState<Product | null>(null);
    const [variants, setVariants] = useState<Variant[]>([]);
    const [allowedActions, setAllowedActions] = useState([]);
    const [buttons, setButtons] = useState<StateButton[]>([]);
    const [openModal, setOpenModal] = useState(false);

    async function fetchData() {
        // fetch data
        const productResponse = await getProductById(params.id, true);
        const allowedActionsResponse = await getAllowedActions(params.id);

        const productJson = await productResponse.json();
        const variantsJson = productJson.data.variants;
        const allowedActionsJson = await allowedActionsResponse.json();
        console.log("allowedActionsJson", allowedActionsJson);

        // state
        setProduct(productJson.data);
        setVariants(variantsJson);
        setAllowedActions(allowedActionsJson.data);
        setButtons(
            orderStateButtons.filter((x) =>
                allowedActionsJson?.includes(x.state)
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
        await updateProduct(params.id, "", formData);
        await fetchData();
        handleCloseModal();
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
                ></CustomDataGrid>
            </div>
            <Dialog open={openModal} onClose={handleCloseModal}>
                <DialogTitle>Edit Product</DialogTitle>
                <DialogContent>
                    {product && (
                        <ProductForm
                            product={product}
                            variants={variants}
                            onSubmit={handleSubmitForm}
                        />
                    )}
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleCloseModal}>Cancel</Button>
                </DialogActions>
            </Dialog>
        </>
    );
}
