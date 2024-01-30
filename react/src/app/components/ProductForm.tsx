// ProductForm.tsx
import React, { useState } from "react";
import { Product } from "@/lib/product";
import { TextField, Button, Grid } from "@mui/material";

interface ProductFormProps {
    product: Product;
    onSubmit: (formData: Product) => void;
}

const ProductForm: React.FC<ProductFormProps> = ({ product, onSubmit }) => {
    const [formData, setFormData] = useState<Product>(product);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        onSubmit(formData);
    };

    return (
        <form onSubmit={handleSubmit}>
            <Grid container spacing={2}>
                <Grid item xs={12}>
                    <TextField
                        fullWidth
                        label="Name"
                        variant="outlined"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                    />
                </Grid>
                <Grid item xs={12}>
                    <TextField
                        fullWidth
                        label="Description"
                        variant="outlined"
                        name="description"
                        value={formData.description}
                        onChange={handleInputChange}
                    />
                </Grid>
                {}
            </Grid>
            <Button
                type="submit"
                variant="contained"
                color="primary"
                sx={{ mt: 2 }}
            >
                Save
            </Button>
        </form>
    );
};

export default ProductForm;
