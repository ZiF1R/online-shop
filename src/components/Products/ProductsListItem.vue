<template>
  <li class="item">
    <div class="item__content">
      <img :src="item?.photo_link" width="100" />
      <div class="item__info">
        <h2>
          <router-link :to="link">{{item.name}}</router-link>
        </h2>
        <ProductPropertiesList v-if="item?.properties" :properties="item.properties" :limit="5" />
      </div>
    </div>
    <div class="item__other-info">
      <div class="item__stats">
        <span class="item__price">{{item.price}}&nbsp;<small>p.</small></span>
        <div v-if="rating" class="rating">
          <span class="rate">{{rating}}</span>
          <IconStar />
          <span class="rates_count">({{item.rates_count}})</span>
        </div>
      </div>
      <button v-if="!cart.isInCart(item)" class="to-cart" @click="cart.addToCart(item)">
        В корзину
      </button>
      <router-link v-else to="/cart" class="link_secondary">
        В корзине
      </router-link>
      <button v-if="remove" class="button_danger" @click="emit('onItemRemove', item)">
        Удалить
        <IconTrash color="#fff" />
      </button>
    </div>
  </li>
</template>

<script setup lang="ts">
import type {Product} from "types/main.types";
import {computed} from "vue";
import IconStar from "components/Icons/IconStar.vue";
import ProductPropertiesList from "components/Products/ProductPropertiesList.vue";
import {useCartStore} from "stores/cart";
import {useUserStore} from "stores/user";
import IconTrash from "components/Icons/IconTrash.vue";

const props = defineProps({
  item: Object,
  remove: {
    type: Boolean,
    required: false,
    default: false,
  },
});
const emit = defineEmits(["onItemRemove"]);

const user = useUserStore();
const cart = useCartStore();
const rating = computed(() => Number(props.item.rating));
const link = computed(() => {
  return `/categories/${props.item.category_id}/products/${props.item.code}`;
});
</script>

<style scoped>
h2 {
  font-size: 16px;
}

img {
  height: fit-content;
  width: 130px;
}

.link_secondary {
  text-align: center;
}

.item__stats {
  margin-bottom: 15px;
}

.item {
  display: flex;
  justify-content: space-between;
  margin: 30px 0 50px;
}

.item__content {
  display: flex;
  gap: 30px;
}

.item__info {
  margin-right: 20px;
}

.item__price {
  font-size: 28px;
}

.item__price > small {
  font-size: 16px;
}

.rating {
  display: flex;
  align-items: center;
}

.rate {
  font-weight: bold;
  margin-right: 5px;
}

.rates_count {
  margin-left: 10px;
}

.item__other-info {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.button_danger {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
  padding: 7px 30px;
  border: none;
}
</style>