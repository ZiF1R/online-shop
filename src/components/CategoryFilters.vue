<template>
  <ul class="filters">
    <li class="filter" v-for="(filter, i) in filters" :key="filter.property.name">
      <details :open="i < 5">
        <summary>{{filter.property.name}}</summary>
        <div v-if="filter.property.type === 'String'" class="values">
          <label class="value" v-for="value in filter.values" :key="value">
            <input
                type="checkbox"
                :name="filter.property.name"
                v-model="activeFilters[filter.property.name]"
                :value="value"
            >
            {{value}}
          </label>
        </div>
        <template v-else>
          от
          <input
              type="number"
              :placeholder="filter.values.min"
              :min="filter.values.min"
              :max="filter.values.max"
              :name="filter.property.name"
              v-model="activeFilters[filter.property.name]['from']"
          >
          до
          <input
              type="number"
              :placeholder="filter.values.max"
              :min="activeFilters[filter.property.name]['from'] || filter.values.min"
              :max="filter.values.max"
              :name="filter.property.name"
              v-model="activeFilters[filter.property.name]['to']"
          >
          {{filter.property.designation}}
        </template>
      </details>
    </li>
    <li class="show-products">
      <button @click="emit('onFilter', activeFilters)">Показать товары</button>
      <span @click="activeFilters = {}" class="link_primary">Очистить форму</span>
    </li>
  </ul>
</template>

<script setup lang="ts">
import {computed, defineProps, onMounted, ref, watch} from "vue";
import type {Category} from "types/menu.types";
import {getCategoryFilters} from "services/categories.service";
import type {Filters} from "types/main.types";
import {useRoute} from "vue-router";

const route = useRoute();
const props = defineProps<{
  category: Category
}>();

const emit = defineEmits(["onFilter"]);

const activeFilters = ref(
    route.query?.filters ? JSON.parse(route.query.filters) : {}
);
const categoryFilters = ref<Filters>([]);
const filters = computed(() => {
  return categoryFilters.value.reduce((acc, filter) => {
    const item = {...filter};
    if (!(filter.property.name in activeFilters.value)) {
      if (filter.property.type === "String") {
        activeFilters.value[filter.property.name] = [];
      } else {
        activeFilters.value[filter.property.name] = {};
      }
    }

    if (filter.property.type === "String") {
      item.values = Array.from(new Set(item.values.map((value) => value.value)));
    }
    return [...acc, item];
  }, []);
});

watch(
    () => props.category,
    async () => {
      categoryFilters.value = await getCategoryFilters(+props.category.id);
    },
    {immediate:true}
);
</script>

<style scoped>
.filters {
  display: flex;
  flex-direction: column;
  height: fit-content;
  width: 35%;
  padding: 30px 30px 0;
  gap: 10px;

  border-radius: 2px;
  background: var(--background-primary);
}

summary {
  font-size: 16px;
  margin-bottom: 5px;
}

.values {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.show-products {
  position: sticky;
  bottom: 0;
  padding: 20px 0;
  background: var(--background-primary);
}

.show-products button {
  width: 100%;
}
</style>