<template>
  <li class="cart__product">
    <div class="product__content">
      <img :src="item.photo_link" alt="photo" />
      <div class="product__info">
        <h2>
          <router-link :to="linkPath">{{item.name}}</router-link>
        </h2>
        <span class="product__code">#{{item.code}}</span>
        <CartListItemCounter class="counter" @onChange="changeCount" :count="count" />
      </div>
    </div>
    <div class="product__other-info">
      <span class="item__price">{{price}}&nbsp;<small>p.</small></span>
      <button class="button_danger" @click="emit('onItemRemove', item)">
        Удалить
        <IconTrash color="#fff" />
      </button>
    </div>
  </li>
</template>

<script setup lang="ts">
import CartList from "components/Cart/CartList.vue";
import type {Product} from "types/main.types";
import {computed, ref} from "vue";
import IconTrash from "components/Icons/IconTrash.vue";
import CartListItemCounter from "components/Cart/CartListItemCounter.vue";
import {useCartStore} from "stores/cart";

const emit = defineEmits(["onCountChange", "onItemRemove"]);
const props = defineProps<{
  item: Product
}>();
const linkPath = computed(() => {
  return '/categories/' + props.item.category_id + '/products/' + props.item.code;
});
const count = ref<number>(props.item.order_count || 1);
const price = computed(() => Number(+props.item.price * count.value).toFixed(2));

function changeCount(newCount: number) {
  count.value = newCount;
  emit("onCountChange", {
    item: props.item,
    count: count.value,
  });
}
</script>

<style scoped>
h2 {
  font-size: 16px;
}

img {
  height: fit-content;
  width: 130px;
}

.cart__product {
  display: flex;
  justify-content: space-between;
  margin: 30px 0 50px;
}

.product__content {
  display: flex;
  gap: 30px;
}

.product__info {
  margin-right: 20px;
}

.item__price {
  font-size: 28px;
}

.item__price > small {
  font-size: 16px;
}

.product__other-info {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.button_danger {
  display: flex;
  align-items: center;
  gap: 10px;
}

.counter {
  margin-top: 25px;
}
</style>