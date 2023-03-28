<template>
  <div v-if="route.path !== '/'" class="wrapper">
    <template v-for="point in path" :key="point.name">
      <router-link v-if="point.path" :to="point.path">
        {{point.name}}
      </router-link>
      <span v-else>{{point.name}}</span>
      <span class="divider">/</span>
    </template>
  </div>
</template>

<script setup lang="ts">
import {useRoute} from "vue-router";
import {ref, watch} from "vue";
import {getCategory, getCategoryRootSection} from "services/categories.service";
import {getProduct} from "services/products.service";

const route = useRoute();

type Route = {
  name: string,
  path: string|null,
}
const root = ref<Route>({
  name: "Главная",
  path: "/",
});
const section = ref<Route|null>(null);
const category = ref<Route|null>(null);
const path = ref<Route[]>([]);

watch(
    () => route.params,
    async (newVal, oldVal) => {
      const currentPath = await getRouteFromQueryParams();
      path.value = [root.value].concat(currentPath);
    }
);

async function getRouteFromQueryParams(): Promise<any> {
  const path: any = [];
  const {code, category_id} = route.params;

  if (category_id) {
    const rootCategory = await getCategory(+category_id);
    const rootSection = await getCategoryRootSection(+rootCategory.section_id);
    section.value = {
      name: rootSection.name,
      path: "/sections/" + rootSection.id,
    };
    path.push(section.value);
  }

  if (code) {
    const rootCategory = await getCategory(+category_id);
    category.value = {
      name: rootCategory.name,
      path: "/categories/" + category_id,
    };
    path.push(category.value);
  }

  return path;
}
</script>

<style scoped>
.divider {
  margin: 0 10px;
}
</style>