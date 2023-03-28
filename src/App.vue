<template>
  <AppBar />
  <AppHeader />
  <BreadCrumbs />
  <router-view />
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
import AppHeader from "components/AppHeader.vue";
import BreadCrumbs from "components/BreadCrumbs.vue";
import AppBar from "components/AppBar.vue";
</script>

<style>
header {
  display: flex;
  position: relative;
  width: 100%;
  align-items: center;
  justify-content: center;

  line-height: 1.5;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}
</style>
