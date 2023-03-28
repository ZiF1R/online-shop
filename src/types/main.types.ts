export * from "./menu.types";

export type User = {
  id: number,
  mail: string,
  password: string,
  is_admin: number,
  name: string|null,
  phone: string|null,
  birth: Date|null,
}

export type Product = {
  code: number,
  category_id: number,
  brand_id: number,
  name: string,
  category_name?: string,
  brand_name?: string,
  price: number,
  description: string | null,
  count: number,
  photo_link: string | null,
  discount: number | null,
  rating?: number,
  properties?: Array<Property>,
  feedback?: Array<Testimonial>,
};

export type Testimonial = {
  id: number,
  product_code: number,
  user_id: number,
  user_name: string,
  created: Date,
  comment: string,
  rating: number,
  reply_comment_id: number,
}

export type Feedback = {
  product_code: number,
  rating: number,
  comment: string,
  reply_comment_id: number|null,
}

export type Property = {
  name: string,
  category_id: number,
  type: string,
  designation: string|null,
}

export type Filters = Array<{
  property: Property,
  values: {
    min: number,
    max: number
  } | Array<String>,
}>;

export type Products = Array<Product>;

export type GroupedProducts = {
  [key: string]: Array<Product>,
};

export type SelectOption = {
  name: string,
  value: string,
};

export type Filter = Array<String>|{from: number, to: number};

export type ProductsSearchOptions = {
  search?: string,
  filters?: string,
  orderBy?: string
  order?: string
}