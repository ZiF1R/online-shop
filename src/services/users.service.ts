import axios from "axios";
import type {User} from "types/main.types";

export const getUser = async (email: string, password: string) => {
  const response = await axios.get(`/users?email=${email}&password=${password}`);
  return response.data;
}

export const getUserById = async (id: number) => {
  const response = await axios.get(`/users/${id}`);
  return response.data?.user;
}

export const createUser = async (data: User) => {
  const response = await axios.post(`/users`, JSON.stringify(data));
  return response.data;
}

export const changeUserData = async (data: User) => {
  const response = await axios.post(`/users/${data.id}`, JSON.stringify(data));
  return response.data?.user;
}