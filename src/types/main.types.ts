export * from "./menu.types";

export type Product = {
    code: number,
    category_id: number,
    brand_id: number,
    name: string,
    category_name: string,
    brand_name: string,
    price: number,
    description: string|null,
    count: number,
    photo_link: string|null,
    discount: number|null,
}

export type GroupedProducts = {
    [key: string]: Array<Product>
}