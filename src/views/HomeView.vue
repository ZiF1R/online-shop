<template>
  <main class="wrapper">
    <h1>Разделы</h1>
    <div class="sections">
      <div class="sections__section" v-for="section in menu" :key="section.section.id">
        <router-link class="section-link" :to="'/sections/' + section.section.id">
          <h2>{{section.section.name}}</h2>
        </router-link>
        <div class="categories">
          <router-link
              class="category-link"
              :to="'/categories/' + category.id"
              v-for="category in section.categories"
              :key="category.id"
          >
            {{category.name}}
          </router-link>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {getAllSections, getMenu, getSection, getSectionCategories} from "services/menu.service";
import type {Menu, Section} from "types/menu.types";
import {useRoute} from "vue-router";

const route = useRoute();
const menu = ref<Menu>([]);

onMounted(async () => {
  menu.value = await getMenu();
});
</script>

<style scoped>
.categories {
  display: flex;
  flex-wrap: wrap;
  gap: 30px 30px;
}

.sections__section {
  margin-bottom: 40px;
}

h2 {
  margin-bottom: 20px;
}

.section-link {
  color: var(--color-text);
  text-decoration: underline;
}

.category-link {
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
