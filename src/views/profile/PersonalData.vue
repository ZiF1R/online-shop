<template>
  <h1>Личные данные</h1>
  <div class="fields">
    <CustomInput type="text" v-model="currentUser.name" label="Имя" />
    <CustomInput type="email" v-model="currentUser.mail" label="Почта" />
    <CustomInput type="tel" v-model="currentUser.phone" label="Телефон" />
    <CustomInput type="date" v-model="currentUser.birth" label="Дата рождения" />

    <button class="button_primary" @click="saveChanges">Сохранить</button>
  </div>
</template>

<script setup lang="ts">
import {useUserStore} from "stores/user";
import {computed, ref} from "vue";
import {changeUserData} from "services/users.service";
import CustomInput from "components/CustomElements/CustomInput.vue";
const user = useUserStore();
const currentUser = computed(() => ({...user.getUser()}));

async function saveChanges() {
  if (currentUser.value.name && currentUser.value.mail) {
    const newUserData = await changeUserData(currentUser.value);
    user.setUser(newUserData);
  }
}
</script>

<style scoped>
.fields {
  display: flex;
  flex-direction: column;
  width: 400px;
}

.button_danger,
.button_primary {
  margin-top: 15px;
  width: fit-content;
}
</style>