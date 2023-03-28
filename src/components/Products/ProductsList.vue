<template>
  <ul class="list">
    <li>Всего: {{products.length}}</li>
    <ProductsListItem
        v-for="product in products"
        :item="product"
        :key="product.id"
        :remove="remove"
        @onItemRemove="emit('onItemRemove', $event)" />
  </ul>
</template>

<script setup lang="ts">
import type {Products} from "types/main.types";
import ProductsListItem from "components/Products/ProductsListItem.vue";

const props = defineProps({
  products: Object,
  remove: {
    type: Boolean,
    required: false,
    default: false,
  },
});
const emit = defineEmits(["onItemRemove"]);
</script>

<style scoped>
.list {
  width: 65%;
}
</style>