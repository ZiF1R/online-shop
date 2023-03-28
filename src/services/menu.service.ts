import axios from "axios";
import type { Menu, Section } from "@/types/menu.types";

export const createSection = async (name: string) => {
  const response = await axios.post(`/sections`, JSON.stringify({name}));
  return response.data?.section;
}

export const createCategory = async (section_id: number, name: string) => {
  const response = await axios.post(`/categories`, JSON.stringify({section_id, name}));
  return response.data?.category;
}

export const deleteSection = async (id: number) => {
  await axios.delete(`/sections`, { data: JSON.stringify({id})});
}

export const deleteCategory = async (id: number) => {
  await axios.delete(`/categories`, { data: JSON.stringify({id})});
}

export const getSectionCategories = async (id: number) => {
  const response = await axios.get(`/sections/${id}/categories`);
  return response.data.categories;
};

export const getAllBrands = async () => {
  const response = await axios.get(`/brands`);
  return response.data.brands;
}

export const getSection = async (id: number) => {
  const response = await axios.get("/sections/" + id);
  return response.data.section;
}

export const getAllSections = async () => {
  const response = await axios.get("/sections");
  return response.data.sections;
};

export const getMenu = async (): Promise<Menu> => {
  const sections: Array<Section> = await getAllSections();

  const result: Menu = [];
  for (const section of sections) {
    const categories = await getSectionCategories(+section.id);
    result.push({
      section,
      categories,
    });
  }

  return result;
};
