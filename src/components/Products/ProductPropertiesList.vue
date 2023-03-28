<template>
  <ul class="item__properties">
    <li class="property" v-for="property in properties.slice(0, limit || properties.length)" :key="property.name">
      <span class="property__name">{{property.name}}</span>
      <span class="property__value">
        {{property.value}} {{property.designation}}
      </span>
    </li>
  </ul>
</template>

<script setup lang="ts">
const props = defineProps(["properties", "limit"]);
</script>

<style scoped>
.property {
  font-size: 14px;
}

.property__name {
  margin-right: 10px;
  color: rgba(30, 30, 30, 0.4);
}
</style>