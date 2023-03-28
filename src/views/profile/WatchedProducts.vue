<template>
  <template v-if="user.isAuthorized">
    <div class="header">
      <h1>Просмотренные товары</h1>
      <button v-if="products?.length" class="button_secondary" @click="openModal = true">
        Очистить
        <IconTrash />
      </button>
    </div>
    <ProductsList
        class="list"
        v-if="products?.length"
        :products="products"
        :remove="true"
        @onItemRemove="removeWatchProduct"
    />
    <p v-else>Вы пока не просмотрели ни один товар</p>
  </template>
  <p v-else>Войдите для доступа к данной странице</p>

  <AppModal v-if="openModal" @onClose="openModal = false">
    <template #header>
      <h2>Вы уверены?</h2>
    </template>
    <template #content>
      Отменить действие будет невозможно!
    </template>
    <template #buttons>
      <button @click="clearWatchHistory">Очистить</button>
    </template>
  </AppModal>
</template>

<script lang="ts" setup>
import ProductsList from "components/Products/ProductsList.vue";
import {useUserStore} from "stores/user";
import {onMounted, ref} from "vue";
import {getWatchedProducts, removeWatchedProduct} from "services/watched-products.service";
import IconTrash from "components/Icons/IconTrash.vue";
import AppModal from "components/AppModal.vue";

const user = useUserStore();
const products = ref([]);
const openModal = ref(false);

onMounted(async () => {
  if (user.isAuthorized) {
    products.value = await getWatchedProducts(+user.getUser().id);
  }
});

async function removeWatchProduct(item) {
  await removeWatchedProduct(+user.getUser().id, +item.code);
  products.value = await getWatchedProducts(+user.getUser().id);
}

async function clearWatchHistory() {
  for (const product of products.value) {
    await removeWatchedProduct(+user.getUser().id, +product.code);
  }
  products.value = await getWatchedProducts(+user.getUser().id);
  openModal.value = false;
}
</script>

<style scoped>
h1 {
  margin-bottom: 20px;
}

.list {
  width: 100%;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

button.button_secondary {
  display: flex;
  text-align: center;
  align-items: center;
  height: fit-content;
  gap: 10px;
}
</style>