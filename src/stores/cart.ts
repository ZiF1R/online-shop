import {ref, computed, reactive, onMounted} from "vue";
import { defineStore } from "pinia";
import type {Product, Products} from "types/main.types";
import {getProduct} from "services/products.service";
import {createOrder} from "services/orders.service";
import {useUserStore} from "stores/user";

export const useCartStore = defineStore("cart", () => {
  const cart = ref<Products>([]);
  const productCodes = computed(() => {
    return cart.value.reduce((acc, product) => {
      acc.push(+product.code);
      return acc;
    }, []);
  });

  async function order(user_id: number) {
    await createOrder(user_id, cart.value);
    clearCart();
  }

  onMounted(async () => {
    const localStorageCart = localStorage.getItem("cart");
    if (localStorageCart) {
      const codes = JSON.parse(localStorageCart);
      for (const code of codes) {
        const product = await getProduct(+code);
        cart.value.push(product);
      }
    }
  });

  function saveToLocalStorage() {
    localStorage.setItem("cart", JSON.stringify(productCodes.value));
  }

  function addToCart(product: Product) {
    if (!isInCart(product)) {
      product.order_count = 1;
      cart.value.push(product);
      saveToLocalStorage();
    }
  }

  function setProductCount(code: number, count: number) {
    cart.value.map(p => {
      if (+p.code === +code) {
        p.order_count = count;
      }

      return p;
    });
  }

  function isInCart(product: Product): boolean {
    return cart.value.filter(p => +p.code === +product.code).length > 0;
  }

  function removeFromCart(code: number) {
    cart.value = cart.value.filter(p => +p.code !== +code);
    saveToLocalStorage();
  }

  function clearCart() {
    cart.value = [];
    saveToLocalStorage();
  }

  function getCart() {
    return cart.value;
  }

  function getCartLength() {
    return cart.value.length;
  }

  return { getCart, addToCart, getCartLength, removeFromCart, isInCart, clearCart, order, setProductCount };
});
