import axios from "axios";
import type { Menu, Section } from "@/types/menu.types";

export const test = async () => {
  const obj = { name: "newSection" };
  // const request = axios.post("/sections", JSON.stringify(obj));
  const request = axios.get("/products?search=Ламинат");
  // const request = axios.get("/sections/11");
  // const request = axios.get("/sections");
  return await request;
};

export const getSectionCategories = async (id: number) => {
  return await axios.get(`/sections/${id}/categories`);
};

export const getAllSections = async () => {
  return await axios.get("/sections");
};

export const getMenu = async (): Promise<Menu> => {
  const response = await getAllSections();
  const sections: Array<Section> = response.data.sections;

  const result: Menu = [];
  for (const section of sections) {
    const categoriesResponse = await getSectionCategories(+section.id);
    result.push({
      name: section.name,
      categories: categoriesResponse.data.categories,
    });
  }

  return result;
};
