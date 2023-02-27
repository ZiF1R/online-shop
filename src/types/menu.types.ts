export type Section = {
  id: number;
  name: string;
};
export type Category = {
  id: number;
  section_id: number;
  name: string;
};
export type Menu = Array<{ name: string; categories: Array<Category> }>;
