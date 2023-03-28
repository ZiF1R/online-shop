<template>
  <div class="wrapper">
    <p v-if="!user.isAuthorized">Войдите для доступа к этой странице</p>
    <div class="content" v-else>
      <div class="content__active-tab">
        <router-view></router-view>
      </div>
      <ul class="profile-tabs">
        <li class="tab tab_group-end">
          <router-link to="/profile">
            <IconProfile />
            <span>Личные данные</span>
          </router-link>
        </li>
        <li class="tab">
          <router-link to="/cart">
            <IconCart />
            <span>Корзина</span>
          </router-link>
        </li>
        <li class="tab">
          <router-link to="/profile/favourites">
            <IconStar />
            <span>Избранные товары</span>
          </router-link>
        </li>
        <li class="tab">
          <router-link to="/profile/watched">
            <IconEye />
            <span>Просмотренные товары</span>
          </router-link>
        </li>
        <li class="tab tab_group-end">
          <router-link to="/profile/orders">
            <IconBook />
            <span>История заказов</span>
          </router-link>
        </li>
        <li class="tab">
          <div @click="user.resetUser()">
            <IconLogout />
            <span>Выход</span>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useUserStore} from "stores/user";
import {h, ref} from "vue";
import IconProfile from "components/Icons/IconProfile.vue";
import IconCart from "components/Icons/IconCart.vue";
import IconStar from "components/Icons/IconStar.vue";
import IconEye from "components/Icons/IconEye.vue";
import IconBook from "components/Icons/IconBook.vue";
import IconLogout from "components/Icons/IconLogout.vue";

const user = useUserStore();
</script>

<style scoped>
.content {
  display: flex;
  gap: 50px;
}

.content__active-tab {
  width: 75%;
}

.profile-tabs {
  width: 25%;
}

li a, li div {
  display: flex;
  gap: 10px;
  margin: 10px 0;
  cursor: pointer;
}

li svg {
  width: 20px;
  height: 20px;
}
</style>