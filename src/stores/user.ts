import {ref, computed, reactive, onMounted} from "vue";
import { defineStore } from "pinia";
import type {User} from "types/main.types";
import {getProduct} from "services/products.service";
import {getUserById} from "services/users.service";

export const useUserStore = defineStore("user", () => {
  const user = ref<User|null>(null);

  onMounted(async () => {
    const localStorageUser = localStorage.getItem("user");
    if (localStorageUser) {
      const user_id = localStorageUser;
      user.value = await getUserById(+user_id);
    }
  });

  function saveToLocalStorage() {
    localStorage.setItem("user", user.value.id);
  }

  function setUser(data: User): void {
    user.value = data;
    saveToLocalStorage();
  }

  function getUser(): User|null {
    return user.value;
  }

  function resetUser(): void {
    user.value = null;
    localStorage.removeItem("user");
  }

  const isAdmin = computed((): boolean => {
    return Boolean(Number(user.value?.is_admin));
  });

  const isAuthorized = computed((): boolean => {
    return user.value !== null;
  });

  return { setUser, getUser, isAuthorized, resetUser, isAdmin };
});
