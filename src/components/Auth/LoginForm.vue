<template>
  <AppModal @onClose="emit('onClose')">
    <template #header>
      <h2 class="modal__header">Вход</h2>
    </template>
    <template #content>
      <form @submit.prevent="login">
        <CustomInput type="email" placeholder="Почта" v-model.trim="formData.email" required />
        <CustomInput type="password" placeholder="Пароль" v-model.trim="formData.password" required />
        <span class="error">{{errorMessage}}</span>

        <button type="submit">Войти</button>
      </form>
    </template>
    <template #buttons>
      <span class="link_primary" @click="emit('onRedirect')">
        Регистрация
      </span>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import AppModal from "components/AppModal.vue";
import CustomInput from "components/CustomElements/CustomInput.vue";
import {reactive, ref} from "vue";
import {getUser} from "services/users.service";
import {useUserStore} from "stores/user";

const emit = defineEmits(["onClose", "onRedirect", "onSuccess"]);
const formData = reactive({
  email: "",
  password: "",
});

const errorMessage = ref("");

async function login() {
  const response = await getUser(formData.email, formData.password);
  if (response.error) {
    errorMessage.value = response.error;
  } else {
    emit("onSuccess", response.user);
    emit("onClose");
  }
}
</script>

<style scoped>
button {
  width: 100%;
  margin-top: 15px;
}

.error {
  color: coral;
}
</style>