<template>
  <ul class="list">
    <li>Всего: {{products.length}}</li>
    <ProductsListItem :item="product" v-for="product in products" :key="product.id" />
  </ul>
</template>

<script setup lang="ts">
import type {Products} from "types/main.types";
import ProductsListItem from "components/Products/ProductsListItem.vue";
const props = defineProps<{
  products: Products
}>();
</script>

<style scoped>
.list {
  width: 65%;
}
</style>