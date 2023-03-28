<template>
  <div class="search">
    <input type="text" v-model="search" placeholder="Поиск товаров" />
    <button class="search__button">
      <IconSearch />
    </button>
  </div>
  <AppDropDown v-if="showDropDown">
    <SearchProductsList :products="groupedProducts" />
  </AppDropDown>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { searchProducts } from "services/products.service";
import type { GroupedProducts, Product } from "types/main.types";
import IconSearch from "components/Icons/IconSearch.vue";
import AppDropDown from "components/AppDropDown.vue";
import SearchProductsList from "components/Search/SearchProductsList.vue";

const showDropDown = ref(false);
const search = ref("");
const products = ref<Array<Product>>([]);
const groupedProducts = computed<GroupedProducts>(() => {
  return products.value.reduce((acc, product) => {
    const category = product.category_name;
    if (category in acc) {
      acc[category].push(product);
    } else {
      acc[category] = [product];
    }
    return acc;
  }, {} as GroupedProducts);
});

watch(
  () => search.value,
  async () => {
    showDropDown.value = false;
    products.value = await searchProducts(search.value);
    showDropDown.value = search.value.length > 0;
  }
);
</script>

<style scoped lang="scss">
.search {
  display: flex;
  align-items: center;
}

.search__button {
  border: none;
  outline: none;
  background: var(--color-primary);
  cursor: pointer;
  padding: 10px;
  box-shadow: var(--shadow);

  &:hover {
    background: lighten(#FFA726, 2%);
  }
}

input {
  outline: none;
  border: none;
  box-shadow: var(--shadow);
  padding: 14px;
  min-width: 400px;
}
</style>
