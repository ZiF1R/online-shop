import axios from "axios";
import type {Products} from "types/main.types";

export const createOrder = async (user_id: number, products: Products) => {
  const data = [];
  for (const product of products) {
    data.push({
      code: product.code,
      order_count: product.order_count,
    });
  }
  const response = await axios.get(`/orders?user_id=${user_id}&products=${JSON.stringify(data)}`);
  return response.data;
}
