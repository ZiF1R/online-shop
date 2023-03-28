<template>
  <AppModal @onClose="emit('onClose')">
    <template #header>
      <h2 class="modal__header">Регистрация</h2>
    </template>
    <template #content>
      <form @submit.prevent="registrate">
        <CustomInput type="text" placeholder="Имя" v-model.trim="formData.name" required />
        <CustomInput type="email" placeholder="Почта" v-model.trim="formData.email" required />
        <CustomInput type="password" placeholder="Пароль" v-model.trim="formData.password" required />
        <CustomInput type="password" placeholder="Повторите пароль" v-model.trim="formData.repeatPassword" required />
        <span class="error">{{errorMessage}}</span>

        <button type="submit">Зарегистрироваться</button>
      </form>
    </template>
    <template #buttons>
      <span class="link_primary" @click="emit('onRedirect')">
        Вход
      </span>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import AppModal from "components/AppModal.vue";
import CustomInput from "components/CustomElements/CustomInput.vue";
import {reactive, ref} from "vue";
import {createUser} from "services/users.service";

const emit = defineEmits(["onClose", "onRedirect", "onSuccess"]);
const formData = reactive({
  name: "",
  email: "",
  password: "",
  repeatPassword: "",
});

const errorMessage = ref("");

async function registrate() {
  if (formData.password === formData.repeatPassword) {
    const response = await createUser(formData);
    if (response.error) {
      errorMessage.value = response.error;
    } else {
      emit("onSuccess", response.user);
      emit("onClose");
    }
  } else {
    errorMessage.value = "Пароли не совпадают";
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