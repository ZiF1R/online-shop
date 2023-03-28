<template>
  <header id="header">
    <router-link to="/" class="logo">
      <IconLogo />
    </router-link>
    <app-search />
    <app-menu />
  </header>
</template>

<script setup lang="ts">
import AppMenu from "components/AppMenu.vue";
import AppSearch from "components/Search/AppSearch.vue";
import IconLogo from "components/Icons/IconLogo.vue";
</script>

<style>
header {
  display: flex;
  align-items: center;
  position: relative;
  background: var(--background-primary);
  gap: 15px;
}

.logo {
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo > svg {
  height: 15px;
}

.logo:hover {
  background: none;
}
</style>