import axios from "axios";
import type {Category, Section} from "types/menu.types";

export const getCategory = async (id: number): Promise<Category> => {
  const response = await axios.get(`/categories/${id}`);
  return response.data?.category;
};

export const getCategoryRootSection = async (id: number): Promise<Section> => {
  const response = await axios.get(`/sections/${id}`);
  return response.data?.section;
}

export const getCategoryFilters = async (id: number) => {
  const response = await axios.get(`/categories/${id}/filters`);
  return response.data?.filters;
}