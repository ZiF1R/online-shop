<template>
  <input type="text" v-model="search">
</template>

<script setup lang="ts">
import {computed, reactive, ref, watch} from "vue";
import {searchProducts} from "@/services/products.service";
import type {GroupedProducts, Product} from "@/types/main.types";

const search = ref("");
let products: Array<Product> = reactive([]);
let groupedProducts: GroupedProducts = reactive({});

watch(
    () => search.value,
    async () => {
      products = await searchProducts(search.value);
      groupedProducts = groupProducts();
      console.log(groupedProducts);
    }
);

function groupProducts(): GroupedProducts {
  return products.reduce((acc, product) => {
    const category = product.category_name;
    if (category in acc) {
      acc[category].push(product);
    } else {
      acc[category] = [product];
    }
    return acc;
  }, {} as GroupedProducts)
}
</script>

<style scoped>

</style>