"use client";

import { CustomDataGrid } from "@/app/components/CustomDataGrid";
import { useEffect, useState } from "react";
import { orderStateButtons, Button as StateButton } from "@/lib/buttons";
import { Button } from "@mui/material";
import { getAllowedActions, getProductById, updateProduct } from "@/lib/api";
import { Product } from "@/lib/product";

export default function Payment({ params }: { params: { id: number } }) {
    const [payment, setPayment] = useState<Product>();
    const [allowedActions, setAllowedActions] = useState([]);
    const [buttons, setButtons] = useState<StateButton[]>([]);

    async function fetchData() {
        // fetch data
        const payment = await getProductById(params.id);
        const allowedActions = await getAllowedActions(params.id);

        const allowedActionsJson = await allowedActions.json();
        const paymentJson = await payment.json();

        // state
        setPayment(paymentJson.data);
        setAllowedActions(allowedActionsJson.data);
        console.log("allowedActionsJson.data", allowedActionsJson.data);
        setButtons(
            orderStateButtons.filter((x) =>
                allowedActionsJson?.includes(x.state)
            )
        );
    }

    const updateState = async (path: string) => {
        await updateProduct(params.id, path);
        await fetchData();
    };

    useEffect(() => {
        fetchData();
    }, []);

    return (
        <>
            <div style={{ padding: "5%" }}>
                <CustomDataGrid params={payment}></CustomDataGrid>
            </div>
            <div>
                {buttons?.map((button, index) => (
                    <Button
                        key={index}
                        variant="outlined"
                        sx={{ marginLeft: "80px", color: button.color }}
                        onClick={() => updateState(button.link)}
                    >
                        {button?.text}
                    </Button>
                ))}
            </div>
        </>
    );
}
