<template>
  <div class="comment__header">
    <div class="comment__user">
      <h3>{{item.user_name}}</h3>
      <div class="stars" v-if="!item.reply_comment_id">
        <IconStar v-for="n in +item.rating" :key="n" />
        <IconStar v-for="n in (5 - +item.rating)" :key="n" color="#DDD" />
      </div>
    </div>
    <div class="comment__additional-info">
      <span class="date">{{dateFormat(item.created)}}</span>
      <IconTrash v-if="user.getUser()?.id == item.user_id" class="delete" @click="emit('onPrepareRemove')" />
    </div>
  </div>
  <p class="comment__text">{{item.comment}}</p>
  <button v-if="user.isAuthorized" class="button_secondary" @click="emit('onReply')">
    Ответить
  </button>
</template>

<script setup lang="ts">
import IconStar from "components/Icons/IconStar.vue";
import IconTrash from "components/Icons/IconTrash.vue";
import {useUserStore} from "stores/user";
import type {Testimonial} from "types/main.types";

const emit = defineEmits(["onPrepareRemove", "onReply"]);
const user = useUserStore();
const props = defineProps<{
  item: Testimonial
}>();

function dateFormat(date: Date) {
  return new Date(date).toLocaleDateString();
}
</script>

<style scoped>
.comment__header {
  display: flex;
  justify-content: space-between;
}

.comment__text {
  margin: 15px 0 5px;
}

.comment__user {
  display: flex;
  align-items: center;
  gap: 10px;
}

.stars {
  display: flex;
}

.button_secondary {
  align-self: flex-end;
  width: fit-content;
}

.comment__additional-info {
  display: flex;
  align-items: center;
}

.delete {
  cursor: pointer;
  height: 20px;
  margin-left: 10px;
}

h3 {
  font-weight: bold;
  font-size: 12px;
  text-transform: uppercase;
}
</style>