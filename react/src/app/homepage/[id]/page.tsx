"use client";

import React, { useEffect, useState } from "react";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
} from "@mui/material";
import { CustomDataGrid } from "@/app/components/CustomDataGrid";
import { orderStateButtons, Button as StateButton } from "@/lib/buttons";
import { getAllowedActions, getProductById, updateProduct } from "@/lib/api";
import { Product } from "@/lib/product";
import ProductForm from "@/app/components/ProductForm";

export default function Product({ params }: { params: { id: number } }) {
    const [product, setProduct] = useState<Product | null>(null);
    const [allowedActions, setAllowedActions] = useState([]);
    const [buttons, setButtons] = useState<StateButton[]>([]);
    const [openModal, setOpenModal] = useState(false);

    async function fetchData() {
        // fetch data
        const payment = await getProductById(params.id);
        const allowedActions = await getAllowedActions(params.id);

        const allowedActionsJson = await allowedActions.json();
        const paymentJson = await payment.json();

        // state
        setProduct(paymentJson.data);
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
                <CustomDataGrid params={product}></CustomDataGrid>
                <Button variant="outlined" onClick={handleOpenModal}>
                    Edit Product
                </Button>
            </div>

            <Dialog open={openModal} onClose={handleCloseModal}>
                <DialogTitle>Edit Product</DialogTitle>
                <DialogContent>
                    {product && (
                        <ProductForm
                            product={product}
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
