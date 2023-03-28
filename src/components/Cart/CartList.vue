<template>
  <ul class="cart__products">
    <CartListItem
        v-for="product in cart.getCart()"
        :key="product.code"
        :item="product"
        @onCountChange="changeProductCount"
        @onItemRemove="removeItem"
    />
  </ul>
</template>

<script setup lang="ts">
import CartListItem from "components/Cart/CartListItem.vue";
import {useCartStore} from "stores/cart";
import {computed, ref} from "vue";

const emit = defineEmits(["onTotalPriceChange"]);
const cart = useCartStore();
const itemCounts = ref(
    cart.getCart().reduce((acc, p) => {
      acc[p.code] = p.order_count || 1;
      return acc;
    }, {})
);
const totalCount = computed(() => {
  return Object.values(itemCounts.value).reduce((total, count) => {
    return total += count;
  }, 0);
});
const totalPrice = computed(() => {
  return cart.getCart().reduce((total, product) => {
    return total += itemCounts.value[+product.code] * +product.price;
  }, 0);
});
emit("onTotalPriceChange", {
  totalPrice: totalPrice.value,
  totalCount: totalCount.value,
});

function changeProductCount({item, count}) {
  itemCounts.value[+item.code] = count;
  cart.setProductCount(+item.code, count);
  emit("onTotalPriceChange", {
    totalPrice: totalPrice.value,
    totalCount: totalCount.value,
  });
}

function removeItem(item) {
  cart.removeFromCart(+item.code);
  delete itemCounts.value[item.code];
  emit("onTotalPriceChange", {
    totalPrice: totalPrice.value,
    totalCount: totalCount.value,
  });
}
</script>

<style scoped>

</style>