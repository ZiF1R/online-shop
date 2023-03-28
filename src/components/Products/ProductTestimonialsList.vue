<template>
  <ul class="comments">
    <li v-for="item in list" :key="item.id" class="comments__comment">
      <div class="comment">
        <ProductTestimonialsListItem
            :item="item"
            @onReply="emit('onReply', item.id)"
            @onPrepareRemove="prepareToRemove(item)"
        />
      </div>
      <div class="replies" v-if="item?.replies?.length">
        <details>
          <summary>Показать ответы ({{item.replies.length}})</summary>
          <ul>
            <li v-for="reply in item.replies" :key="reply.id">
              <ProductTestimonialsListItem
                  :item="reply"
                  @onReply="emit('onReply', item.id)"
                  @onPrepareRemove="prepareToRemove(reply)"
              />
            </li>
          </ul>
        </details>
      </div>
    </li>
  </ul>

  <AppModal v-if="openModal" @onClose="openModal = false">
    <template #header>
      <h2>Вы уверены?</h2>
    </template>
    <template #content>
      Отменить действие будет невозможно!
    </template>
    <template #buttons>
      <button @click="removeFeedback">Удалить</button>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import type {Testimonial} from "types/main.types";
import AppModal from "components/AppModal.vue";
import {ref} from "vue";
import {useUserStore} from "stores/user";
import ProductTestimonialsListItem from "components/Products/ProductTestimonialsListItem.vue";

const props = defineProps<{
  list: Array<Testimonial>,
}>();

const user = useUserStore();
const preparedItem = ref(null);
const openModal = ref(false);
const emit = defineEmits(["removeFeedback", "onReply"]);

async function removeFeedback() {
  emit("removeFeedback", preparedItem.value);
  openModal.value = false;
}

function prepareToRemove(item) {
  openModal.value = true;
  preparedItem.value = item;
}
</script>

<style scoped>
.comments {
  display: flex;
  flex-direction: column;
  gap: 30px;
  width: 100%;
}

.comment,
.replies ul > li {
  display: flex;
  flex-direction: column;
  background: var(--background-primary);
  padding: 15px 30px;
  border-radius: 2px;
}

.replies {
  margin-top: 10px;
}

details {
  padding-left: 20px;
}

summary {
  cursor: pointer;
  width: fit-content;
}

.replies li {
  width: 95%;
}

.replies ul {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 15px;
  margin-top: 10px;
}

h3 {
  font-weight: bold;
  font-size: 12px;
  text-transform: uppercase;
}
</style>