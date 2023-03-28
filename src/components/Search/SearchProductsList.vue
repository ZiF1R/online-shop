<template>
  <div class="wrapper">
    <div v-for="(category, i) in categories" :key="category" class="category">
      <h3>{{category}}</h3>
      <div class="products">
        <router-link :to="getProductLink(product)" v-for="product in products[i]" :key="product.code" class="product">
          {{product.name}}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {GroupedProducts} from "types/main.types";
import {computed} from "vue";

const props = defineProps<{
  products: GroupedProducts
}>();

function getProductLink(product) {
  return `/categories/${product.category_id}/products/${product.code}`;
}

const categories = computed(() => Object.keys(props.products));
const products = computed(() => Object.values(props.products));
</script>

<style scoped>
.category {
  display: flex;
  position: relative;
  gap: 50px;
  margin-bottom: 10px;
}

h3 {
  width: 10%;
}

.products {
  width: 70%;
}

a {
  display: block;
}
</style>