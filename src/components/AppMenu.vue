<template>
  <nav>
    <div class="nav__button" @click="toggleMenu">
      <IconMenu />
      <IconArrowDown class="arrow" :class="{'arrow_active': openMenu}" />
    </div>
    <AppDropDown v-if="!isLoading && openMenu" @onClose="toggleMenu">
      <ul class="nav__list">
        <li class="nav__section" v-for="section in menu" :key="section.section.id">
          <h3>{{ section.section.name }}</h3>
          <ul class="nav__sublist">
            <li class="nav__category" v-for="category in section.categories" :key="category.id">
              <router-link :to="'/categories/' + category.id" @click="toggleMenu">
                {{ category.name }}
              </router-link>
            </li>
          </ul>
        </li>
      </ul>
    </AppDropDown>
  </nav>
</template>

<script setup lang="ts">
import { getMenu } from "services/menu.service";
import type { Menu } from "types/menu.types";
import { RouterLink } from "vue-router";
import IconMenu from "components/Icons/IconMenu.vue";
import IconArrowDown from "components/Icons/IconArrowDown.vue";
import AppDropDown from "components/AppDropDown.vue";
import {onMounted, ref} from "vue";

const menu = ref<Menu>([]);
const isLoading = ref(true);
const openMenu = ref(false);

onMounted(async () => {
  menu.value = await getMenu() as Menu;
  isLoading.value = false;
});

function toggleMenu() {
  openMenu.value = !openMenu.value;
}
</script>

<style scoped>
h3 {
  margin-bottom: 10px;
}

ul {
  list-style-type: none;
  text-align: left;
  margin: 0;
  padding: 0;
  width: fit-content;
}

.arrow {
  transition: .21s;
}

.arrow_active {
  transform: rotate(180deg);
}

nav {
  width: fit-content;
  padding: 35px 0;
}

.nav__button {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
  padding: 10px;
  box-shadow: var(--shadow);
  background: #ffffff;
  cursor: pointer;
}

.nav__button > svg {
  width: 22px;
}

.nav__list {
  display: flex;
  justify-content: center;
  max-width: 1280px;
  margin: 0 auto;
  gap: 20px;
}
</style>
