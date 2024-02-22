import React, { useState } from "react";
import { TextField, Button, Grid } from "@mui/material";
import { Variant } from "@/lib/variant";

interface VariantFormProps {
    variant: Variant;
    onSubmit: (formData: Variant) => void;
    onClose: () => void;
}

const VariantForm: React.FC<VariantFormProps> = ({
    variant,
    onSubmit,
    onClose,
}) => {
    const [formData, setFormData] = useState<Variant>(variant);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        onSubmit(formData);
    };

    return (
        <form id="variant-form" onSubmit={handleSubmit}>
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
                        label="Price"
                        variant="outlined"
                        name="price"
                        value={formData.price}
                        onChange={handleInputChange}
                    />
                </Grid>
            <Grid item xs={12}>
                    <TextField
                        fullWidth
                        label="Value"
                        variant="outlined"
                        name="value"
                        value={formData.value}
                        onChange={handleInputChange}
                    />
                </Grid>
                <Grid container justifyContent="flex-end" spacing={2} mt={4}>
                    <Button onClick={onClose} style={{ marginRight: "8px" }}>
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        form="variant-form"
                        variant="contained"
                        color="primary"
                    >
                        Save
                    </Button>
                </Grid>
            </Grid>
        </form>
    );
};

export default VariantForm;
