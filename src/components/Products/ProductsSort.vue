<template>
  <div class="sort">
    Сортировать по:
    <custom-select :options="sortings" :activeItem="activeSort" @onSelect="changeSort" />
    <custom-select :options="orders" :activeItem="activeOrder" @onSelect="changeOrder" />
  </div>
</template>

<script setup lang="ts">
import {computed, reactive, ref} from "vue";
import type {SelectOption} from "types/main.types";
import CustomSelect from "components/CustomElements/CustomSelect.vue";
import {useRoute} from "vue-router";

const route = useRoute();
const sortings = reactive<Array<SelectOption>>([
  {
    value: "price",
    name: "цене",
  },
  {
    value: "rating",
    name: "популярности",
  },
  {
    value: "discount",
    name: "скидке",
  },
]);
const orders = reactive<Array<SelectOption>>([
  {
    value: "ASC",
    name: "по возрастанию",
  },
  {
    value: "DESC",
    name: "по убыванию",
  },
]);

const emit = defineEmits(["onSortChange"]);

const activeSort = ref<SelectOption>(
    sortings.filter(s => s.value === route.query?.orderBy)[0] || sortings[0]
);
const activeOrder = ref<SelectOption>(
    orders.filter(o => o.value === route.query?.order)[0] || orders[0]
);
const orderQuery = computed<string>(() =>
    activeSort.value.value + " " + activeOrder.value.value
);

const currentSort = computed(() => ({
  orderBy: activeSort.value,
  order: activeOrder.value
}));

function changeSort(sort: SelectOption) {
  activeSort.value = sort;
  emit("onSortChange", currentSort.value);
}

async function changeOrder(order: SelectOption) {
  activeOrder.value = order;
  emit("onSortChange", currentSort.value);
}
</script>

<style scoped>
.sort {
  display: flex;
}
</style>