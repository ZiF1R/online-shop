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
            <IconProfile color="#000" />
            <span>Личные данные</span>
          </router-link>
          <hr>
        </li>
        <li class="tab">
          <router-link to="/cart">
            <IconCart />
            <span>Корзина</span>
          </router-link>
        </li>
<!--        <li class="tab">-->
<!--          <router-link to="/profile/favourites">-->
<!--            <IconStar />-->
<!--            <span>Избранные товары</span>-->
<!--          </router-link>-->
<!--        </li>-->
        <li class="tab">
          <router-link to="/profile/watched">
            <IconEye />
            <span>Просмотренные товары</span>
          </router-link>
          <hr>
        </li>
<!--        <li class="tab tab_group-end">-->
<!--          <router-link to="/profile/orders">-->
<!--            <IconBook />-->
<!--            <span>История заказов</span>-->
<!--          </router-link>-->
<!--        </li>-->
        <li class="tab">
          <div @click="logout">
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
import {useRouter} from "vue-router";

const user = useUserStore();
const router = useRouter();

function logout() {
  user.resetUser();
  router.push({path: "/"});
}
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
  margin: 7px 0;
  cursor: pointer;
  align-items: center;
  color: var(--color-text);
  padding: 3px 10px;
}

li a:hover {
  background: var(--color-primary-blind);
}

li div {
  color: var(--color-danger);
  transition: .31s;
}

li div:hover {
  background: var(--color-danger-blind);
}

li svg {
  width: 20px;
  height: 20px;
}
</style>