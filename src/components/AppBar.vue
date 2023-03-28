<template>
  <div class="wrapper">
    <div class="location">
      <IconLocation />
      г. Минск
    </div>
    <div class="buttons">
      <router-link to="/cart" class="cart button_secondary profile-link">
        <IconCart />
        Корзина
        <sup v-if="cart.getCartLength() > 0">{{cart.getCartLength()}}</sup>
      </router-link>
      <button v-if="!user.isAuthorized" class="login button_secondary" @click="openLogin = true">
        <IconLogin />
      </button>
      <router-link v-else to="/profile" class="profile-link button_secondary">
        <IconProfile color="#1E1E1E" />
        {{user.getUser().name}}
      </router-link>
      <router-link title="Панель администратора" v-if="user.isAdmin" to="/admin" class="profile-link button_secondary">
        <IconAdmin color="#1E1E1E" />
      </router-link>
    </div>
  </div>

  <LoginForm v-if="openLogin" @onClose="openLogin = false" @onRedirect="redirect" @onSuccess="onFormSuccess" />
  <RegistrationForm v-if="openRegistration" @onClose="openRegistration = false" @onRedirect="redirect"  @onSuccess="onFormSuccess" />
</template>

<script setup lang="ts">
import IconLocation from "components/Icons/IconLocation.vue";
import IconCart from "components/Icons/IconCart.vue";
import IconLogin from "components/Icons/IconLogin.vue";
import LoginForm from "components/Auth/LoginForm.vue";
import IconProfile from "components/Icons/IconProfile.vue";
import RegistrationForm from "components/Auth/RegistrationForm.vue";
import {ref} from "vue";
import {useUserStore} from "stores/user";
import type {User} from "types/main.types";
import {useCartStore} from "stores/cart";
import IconAdmin from "components/Icons/IconAdmin.vue";

const cart = useCartStore();
const user = useUserStore();
const openLogin = ref(false);
const openRegistration = ref(false);

function onFormSuccess(data: User) {
  user.setUser(data);
}

function redirect() {
  openLogin.value = !openLogin.value;
  openRegistration.value = !openRegistration.value;
}
</script>

<style scoped>
.wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0;
  padding: 10px 0;
}

.buttons {
  display: flex;
  gap: 10px;
}

.location {
  display: flex;
  align-items: center;
  gap: 10px;
}

.cart > sup {
  padding: 3px 7px;
  background: var(--color-primary);
  color: #fff;
  font-weight: 600;
}

.button_secondary {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
}
</style>