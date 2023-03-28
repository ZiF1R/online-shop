<template>
  <div class="wrapper">
    <h1>{{product.name}} <small>#{{product.code}}</small></h1>
    <div class="product__content">
      <div class="product__info">
        <div class="info__item" v-if="product?.description">
          <h3>Описание</h3>
          <p>{{product.description}}</p>
        </div>
        <div class="info__item">
          <h3>Характеристики</h3>
          <ProductPropertiesList v-if="product?.properties" :properties="product.properties" />
        </div>
      </div>
      <div class="product__other-info">
        <img :src="product.photo_link">
        <div class="product__stats">
          <span class="product__price">
            {{product.price}} <small>p.</small>
          </span>
          <span class="product__in-stock">
            {{+product.count > 0 ? "В наличии: ✔" : "Нет в наличии: ✖"}}
          </span>
          <button v-if="!cart.isInCart(product)" class="to-cart" @click="cart.addToCart(product)">
            В корзину
          </button>
          <router-link v-else to="/cart" class="to-cart link_secondary">
            В корзине
          </router-link>
        </div>
      </div>
    </div>
    <hr/>
    <div class="product__feedback">
      <div class="feedback__comments">
        <div class="feedback__header">
          <h2>Отзывы</h2>
          <button v-if="user.isAuthorized && !alreadySendFeedback" @click="openModal = true" class="button_secondary">
            Написать отзыв
          </button>
        </div>
        <ProductTestimonialsList
            v-if="product?.feedback?.length"
            :list="product.feedback"
            @removeFeedback="removeFeedback"
            @onReply="sendReply"
        />
        <p v-else>Отзывов пока нет</p>
      </div>
      <div class="feedback__total">
        <ProductRatingTotal v-if="product?.code" :feedback="product?.feedback?.length" :code="+product.code" />
      </div>
    </div>
  </div>

  <AppModal v-if="openModal" @onClose="openModal = false">
      <template #header>
        <h2 class="modal__header" v-if="!feedback.reply_comment_id">Добавить отзыв</h2>
        <h2 class="modal__header" v-else>Напишите ответ</h2>
      </template>
      <template #content>
        <label v-if="!feedback.reply_comment_id">
          Оценка:
          <select name="rating" v-model="feedback.rating">
            <option v-for="n in 5" :value="n" :key="n">{{n}}</option>
          </select>
        </label>
        <textarea v-model.trim="feedback.comment" placeholder="Ваш комментарий"></textarea>
        <span class="error">{{errorMessage}}</span>
      </template>
    <template #buttons>
      <button @click="sendFeedback">Отправить</button>
    </template>
  </AppModal>
</template>

<script lang="ts" setup>
import {getProduct, removeRating, sendRating} from "services/products.service";
import ProductTestimonialsList from "components/Products/ProductTestimonialsList.vue";
import {computed, onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import ProductPropertiesList from "components/Products/ProductPropertiesList.vue";
import type {Feedback, Product} from "types/main.types";
import AppModal from "components/AppModal.vue";
import {useUserStore} from "stores/user";
import ProductRatingTotal from "components/Products/ProductRatingTotal.vue";
import {sendWatchInfo} from "services/watched-products.service";
import {useCartStore} from "stores/cart";

const cart = useCartStore();
const user = useUserStore();
const product = ref<Product|{}>({});
const alreadySendFeedback = computed(() => {
  return product.value?.feedback?.some((f) => +f.user_id == +user.getUser().id);
})
const route = useRoute();
const openModal = ref(false);
const feedback = ref({
  rating: 1,
  comment: "",
  reply_comment_id: null,
});
const errorMessage = ref("");

onMounted(async () => {
  await fetchProduct();
  if (user.isAuthorized) {
    await sendWatchInfo(+user.getUser().id, +product.value.code);
  }
});

function resetFeedbackData() {
  feedback.value = {
    rating: 1,
    comment: "",
    reply_comment_id: null,
  };
}

async function fetchProduct() {
  product.value = await getProduct(+route.params.code);
}

async function sendFeedback() {
  if (feedback.value.comment === "") {
    errorMessage.value = "Необходимо заполнить поле с сообщением";
    return;
  }

  feedback.value.product_code = product.value.code;
  await sendRating(user.getUser().id, feedback.value);
  openModal.value = false;
  resetFeedbackData();
  await fetchProduct();
  errorMessage.value = "";
}

async function sendReply(id) {
  feedback.value.reply_comment_id = id;
  openModal.value = true;
}

async function removeFeedback(item) {
  await removeRating(+product.value.code, +item.id);
  await fetchProduct();
}
</script>

<style scoped>
.to-cart {
  margin-top: 20px;
}

.product__content {
  display: flex;
  flex-direction: row-reverse;
  justify-content: space-between;
}

.feedback__header {
  display: flex;
  justify-content: space-between;
  align-items: start;
}

.wrapper {
  margin-bottom: 50px;
  margin-top: 0;
}

.product__feedback {
  display: flex;
  gap: 50px;
}

.feedback__comments {
  position: relative;
  width: 60%;
}

.product__info {
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 45%;
}

hr {
  margin: 50px 0;
}

.feedback__total {
  width: 40%;
}

.product__other-info {
  position: relative;
  width: 50%;
}

.product__other-info > img {
  height: 400px;
  width: fit-content;
  max-width: 100%;
}

.product__stats {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  margin-top: 15px;
}

.product__price {
  font-size: 28px;
}

.product__price > small {
  font-size: 16px;
}

h1 {
  margin-bottom: 30px;
}

h1 small {
  font-weight: bold;
  font-size: 18px;
  color: #ccc;
}

h2 {
  font-weight: bold;
  margin-bottom: 40px;
}

h3 {
  font-weight: bold;
  font-size: 16px;
  margin-bottom: 10px;
}

p {
  font-size: 14px;
}

textarea {
  outline: none;
  border: 1px solid #ccc;
  resize: none;
  height: 150px;
  width: 100%;
  margin-top: 10px;
}

.modal__header {
  margin-bottom: 0;
}

.error {
  color: coral;
}
</style>