export type Section = {
  id: number;
  name: string;
};
export type Category = {
  id: number;
  section_id: number;
  name: string;
};
export type Menu = Array<{ section: Section; categories: Array<Category> }>;
