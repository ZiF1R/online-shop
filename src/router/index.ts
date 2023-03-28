import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
    },
    {
      path: "/categories/:category_id",
      name: "CategoryProducts",
      component: () => import("views/CategoryProducts.vue"),
    },
    {
      path: "/categories/:category_id/products/:code",
      name: "ProductView",
      component: () => import("views/Product.vue"),
    },
    {
      path: "/admin",
      name: "AdminPanel",
      component: () => import("views/AdminPanel.vue"),
    },
    {
      path: "/profile",
      name: "Profile",
      component: () => import("views/Profile.vue"),
      children: [
        {
          path: "",
          name: "PersonalData",
          component: () => import("views/profile/PersonalData.vue"),
        },
        {
          path: "watched",
          name: "WatchedProducts",
          component: () => import("views/profile/WatchedProducts.vue"),
        },
      ],
    },
    {
      path: "/cart",
      name: "Cart",
      component: () => import("views/Cart.vue"),
    },
    {
      path: "/sections/:section_id",
      name: "Sections",
      component: () => import("views/Sections.vue"),
    },
  ],
});

export default router;
