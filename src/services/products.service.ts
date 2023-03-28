import axios from "axios";
import type {Feedback, Product, ProductsSearchOptions} from "types/main.types";

export const searchProducts = async (search: string): Promise<Array<Product>> => {
  const response = await axios.get(`/products?search=${search}`);
  return response.data?.products;
};

export const filterCategoryProducts = async (category_id: number, options?: ProductsSearchOptions) => {
  let queryParams = "";
  if (options) {
    const {filters, order, orderBy} = options;
    queryParams += `?orderBy=${orderBy || ""}&order=${order || ""}&filters=${filters}`;
  }
  const response = await axios.get(`/categories/${category_id}/products${queryParams}`);
  return response.data?.products;
}

export const getProduct = async (code: number): Promise<Product> => {
  const response = await axios.get(`/products/${code}`);
  return response.data?.product;
}

export const getProductTotalRating = async (code: number) => {
  const response = await axios.get(`/products/${code}/rating`);
  return response.data?.rating;
}

export const sendRating = async (user_id: number, feedback: Feedback) => {
  const {product_code, rating, comment, reply_comment_id} = feedback;

  const data = {
    user_id,
    comment,
    reply_comment_id,
  };

  if (reply_comment_id) {
    data.rating = null;
  } else {
    data.rating = rating;
  }

  const response = await axios.post(`/products/${product_code}/feedback`, JSON.stringify(data));
  return response.data;
}

export const removeRating = async (product_code: number, id: number) => {
  const response = await axios.delete(`/products/${product_code}/feedback`, {data: {id}});
  return response.data;
}

export const createProduct = async (data: Product) => {
  const response = await axios.get(`/products/${data.code}?product=` + JSON.stringify(data));
  console.log(response.data);
  return response.data?.product;
}

export const deleteProduct = async (code: number) => {
  const response = await axios.delete(`/products/${code}`);
  return response.data;
}