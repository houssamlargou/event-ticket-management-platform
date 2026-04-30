<template>
  <header class="border-b border-slate-200 bg-white/95 backdrop-blur sticky top-0 z-40">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
      <RouterLink to="/" class="text-xl font-black tracking-tight text-slate-900">
        ETM
      </RouterLink>

      <div class="flex items-center gap-2 sm:gap-3 flex-wrap justify-end">
        <RouterLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :class="linkClasses(item.to)"
        >
          {{ item.label }}
        </RouterLink>

        <NotificationBell v-if="isOrganizer" />

        <button
          v-if="isAuthenticated"
          @click="logout"
          class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-700 transition-colors"
        >
          Logout
        </button>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";
import api from "../api/axios";
import NotificationBell from "./NotificationBell.vue";
import {
  clearStoredAuth,
  getStoredToken,
  getStoredUser,
  getUserRole,
} from "../utils/auth";

const route = useRoute();
const router = useRouter();
const token = ref(getStoredToken());
const currentUser = ref(getStoredUser());

const syncAuthState = () => {
  token.value = getStoredToken();
  currentUser.value = getStoredUser();
};

watch(
  () => route.fullPath,
  () => {
    syncAuthState();
  },
  { immediate: true }
);

onMounted(() => {
  window.addEventListener("storage", syncAuthState);
});

onBeforeUnmount(() => {
  window.removeEventListener("storage", syncAuthState);
});

const isAuthenticated = computed(() => Boolean(token.value));
const userRole = computed(() => getUserRole(currentUser.value));
const isOrganizer = computed(() => isAuthenticated.value && userRole.value === "organizer");

const navItems = computed(() => {
  if (!isAuthenticated.value) {
    return [
      { label: "Home", to: "/" },
      { label: "Login", to: "/login" },
      { label: "Register", to: "/register" },
    ];
  }

  if (userRole.value === "user") {
    return [
      { label: "Home", to: "/" },
      { label: "Favorites", to: "/favorites" },
      { label: "My Orders", to: "/orders" },
      { label: "Profile", to: "/profile" },
    ];
  }

  if (userRole.value === "organizer") {
    return [
      { label: "Home", to: "/" },
      { label: "My Events", to: "/organizer/events" },
      { label: "Create Event", to: "/create-event" },
      { label: "Profile", to: "/profile" },
    ];
  }

  return [
    { label: "Home", to: "/" },
    { label: "Profile", to: "/profile" },
  ];
});

const linkClasses = (path) => {
  const isHome = path === "/";
  const isActive = isHome ? route.path === path : route.path.startsWith(path);

  return [
    "px-4 py-2 rounded-lg text-sm font-semibold transition-colors",
    isActive
      ? "bg-indigo-50 text-indigo-700"
      : "text-slate-600 hover:text-slate-900 hover:bg-slate-100",
  ];
};

const logout = async () => {
  if (token.value) {
    try {
      await api.post("/logout");
    } catch {
      // Clear local auth even if the server session is already invalid.
    }
  }

  clearStoredAuth();
  syncAuthState();
  router.push("/login");
};
</script>
