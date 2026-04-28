import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import MyOrders from "../pages/MyOrders.vue";
import EventDetails from "../pages/EventDetails.vue";
import CreateEvent from "../pages/CreateEvent.vue";
import EditEvent from "../pages/EditEvent.vue";


const routes = [
  { path: "/", component: Home },
  { path: "/login", component: Login },
  {
    path: "/orders",
    component: MyOrders,
    meta: { requiresAuth: true },
  },
  {path: "/events/:id", component: EventDetails},
  {path: "/create-event", component: CreateEvent},
  {path: "/edit-event/:id", component: EditEvent}
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  if (to.meta.requiresAuth && !token) {
    next("/login");
  } else {
    next();
  }
});

export default router;