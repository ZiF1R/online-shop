<template>
  <ul v-if="!isLoading">
    <li v-for="section in menu" :key="section.name">
      <details>
        <summary>{{ section.name }}</summary>
        <ul>
          <li v-for="category in section.categories" :key="category.id">
            {{ category.name }}
          </li>
        </ul>
      </details>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { onBeforeMount, reactive, ref } from "vue";
import { getMenu } from "@/services/menu.service";
import type { Menu } from "@/types/menu.types";

let menu = reactive<Menu>([]);
const isLoading = ref<boolean>(true);

onBeforeMount(async () => {
  menu = await getMenu();
  isLoading.value = false;
});
</script>

<style scoped></style>
