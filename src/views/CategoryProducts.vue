<template>
  <div class="wrapper">
    <h1 v-if="category">{{category.name}}</h1>
    <ProductsSort @onSortChange="changeSort"/>
    <hr>
    <div class="products__list">
      <ProductsList v-if="products?.length" :products="products" />
      <p class="products-empty" v-else>По данному запросу ничего не найдено</p>
      <CategoryFilters v-if="category" :category="category" @onFilter="filterProducts" />
    </div>
  </div>
</template>

<script setup lang="ts">
import {filterCategoryProducts} from "services/products.service";
import {getCategory} from "services/categories.service";
import type {Category, Filter, Products, ProductsSearchOptions} from "types/main.types";
import {onMounted, ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import ProductsSort from "components/Products/ProductsSort.vue";
import ProductsList from "components/Products/ProductsList.vue";
import CategoryFilters from "components/CategoryFilters.vue";

const route = useRoute();
const router = useRouter();
const category = ref<Category|null>(null);
const products = ref<Products|{}>([]);
const sortOptions = ref<ProductsSearchOptions>({
  order: "",
  orderBy: "",
  filters: "",
});

watch(
  () => route.query,
  async () => await fetchProducts(),
);

onMounted(async () => {
  category.value = await getCategory(+route.params.category_id);
  sortOptions.value = Object.assign(sortOptions.value, route.query);
  router.push({ query: sortOptions.value });
  await fetchProducts();
});

function changeSort(data) {
  const options: ProductsSearchOptions = {
    order: data.order.value,
    orderBy: data.orderBy.value
  }
  sortOptions.value = Object.assign(sortOptions.value, options);
  router.push({ query: sortOptions.value });
}

async function fetchProducts() {
  products.value = await filterCategoryProducts(+route.params.category_id, sortOptions.value);
}

function filterProducts(filters: {[key: string]: Filter}) {
  sortOptions.value.filters = JSON.stringify(filters);
  router.push({ query: sortOptions.value });
}
</script>

<style scoped>
.wrapper {
  margin-top: 0;
}

.products__list {
  display: flex;
  gap: 50px;
}

.products-empty {
  width: 65%;
}

h1 {
  margin-bottom: 20px;
}

hr {
  margin: 20px 0 50px;
}
</style>