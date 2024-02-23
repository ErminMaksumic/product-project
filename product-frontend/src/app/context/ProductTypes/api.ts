import axios from "axios";

const authToken = localStorage.getItem("accessToken");
axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`;

export async function getProductTypeById(id: number): Promise<ProductType[]> {
    let url = `${process.env.NEXT_PUBLIC_URL}/api/productType/${id}`;

    try {
        const response = await axios.get(url);

        return response.data.data;
    } catch (error) {
        console.error("Error fetching product by id:", error);
        throw error;
    }
}

export async function getProductTypes(includeVariant: boolean) {
    try {
        let url = `${process.env.NEXT_PUBLIC_URL}/api/product`;
        if (includeVariant) {
            url = `${process.env.NEXT_PUBLIC_URL}/api/product?includeVariant=true`;
        }

        const response = await axios.get(url);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching products:", error);
        throw error;
    }
}
