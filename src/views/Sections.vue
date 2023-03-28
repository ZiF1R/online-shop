<template>
  <main class="wrapper" v-if="section">
    <h1>{{section.name}}</h1>
    <div class="categories">
      <router-link :to="'/categories/' + category.id" class="category" v-for="category in categories" :key="category.id">
        {{category.name}}
      </router-link>
    </div>
  </main>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {getAllSections, getSection, getSectionCategories} from "services/menu.service";
import type {Category, Section} from "types/menu.types";
import {useRoute} from "vue-router";

const route = useRoute();
const section = ref<Section|{}>({});
const categories = ref<Category[]>([]);

onMounted(async () => {
  section.value = await getSection(+route.params.section_id);
  categories.value = await getSectionCategories(+route.params.section_id);
});
</script>

<style scoped>
.categories {
  display: flex;
  flex-wrap: wrap;
  gap: 30px 30px;
}

a {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 250px;
  height: 150px;
  padding: 30px;
  text-align: center;
  border: 1px solid hsla(160, 100%, 37%, 0.2);
  border-radius: 5px;
}

h1 {
  margin-bottom: 30px;
}
</style>
