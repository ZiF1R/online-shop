import axios from "axios";

export const searchProducts = async (search: string) => {
    const response = await axios.get(`/products?search=${search}`);
    console.log(response.data);
    return response.data?.products;
}