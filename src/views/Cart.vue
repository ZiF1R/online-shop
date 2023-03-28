<template>
  <div class="wrapper">
    <h1>Корзина ({{cart.getCartLength()}})</h1>
    <div class="cart">
      <CartList class="list" @onTotalPriceChange="changeTotalPrice" />
      <div class="cart__total">
        <h2>Итог</h2>
        <span class="products-count">
          <span class="tip">Количество товаров:</span> {{totalCount}}
        </span>
        <span class="products-discount">
          <span class="tip">Скидка:</span> {{discount}}%
        </span>
        <hr>
        <span class="total-price">
          <span class="tip">Общая сумма:</span> {{totalPrice}}&nbsp;<small>p.</small>
        </span>
        <button
            class="prepare-order"
            :class="{'disabled': !cart.getCartLength() || !user.isAuthorized}"
            :disabled="!cart.getCartLength() || !user.isAuthorized"
            @click="makeOrder"
            :title="orderTitle"
        >
          Оформить
        </button>
      </div>
    </div>
  </div>

  <AppModal v-if="openModal" @onClose="openModal = false">
    <template #header>
      <h2>Заказ успешно оформлен</h2>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import {useCartStore} from "stores/cart";
import CartList from "components/Cart/CartList.vue";
import {computed, ref} from "vue";
import AppModal from "components/AppModal.vue";
import {useUserStore} from "stores/user";

const openModal = ref(false);
const openLogin = ref(false);
const cart = useCartStore();
const user = useUserStore();
const totalPrice = ref(0);
const totalCount = ref(0);
const discount = ref(0);
const orderTitle = computed(() => {
  if (user.isAuthorized) {
    return "Нажмите для оформления заказа";
  } else {
    return "Авторизируйтесь для оформления заказа";
  }
});

function changeTotalPrice(data) {
  totalPrice.value = Number(data.totalPrice).toFixed(2);
  totalCount.value = data.totalCount;
}

async function makeOrder() {
  if (user.isAuthorized) {
    await cart.order(user.getUser().id);
    totalPrice.value = 0;
    totalCount.value = 0;
    openModal.value = true;
  }
}
</script>

<style scoped>
h2 {
  margin-bottom: 20px;
}

.cart {
  display: flex;
  gap: 50px;
}

.list {
  width: 70%;
}

.cart__total {
  display: flex;
  flex-direction: column;
  height: fit-content;
  width: 30%;
  padding: 30px 30px;
  gap: 10px;

  border-radius: 2px;
  background: var(--background-primary);
}

.total-price {
  font-size: 26px;
  font-weight: bold;
}

.total-price > small {
  font-size: 16px;
}

.tip {
  font-size: 16px;
  color: #aaa;
}

.prepare-order {
  margin-top: 20px;
}

.disabled {
  background: #eee;
  color: #888;
}
</style>