<template>
  <div ref="rootEl" class="relative">
    <button
      @click="togglePanel"
      class="relative p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
      aria-label="Notifications"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>

      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[11px] font-bold flex items-center justify-center"
      >
        {{ unreadCount }}
      </span>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-3 w-80 max-w-[calc(100vw-2rem)] bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-50"
    >
      <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Notifications</h3>
          <p class="text-xs text-slate-500">Latest event comment alerts</p>
        </div>
        <button
          @click="fetchNotifications"
          class="text-xs font-semibold text-indigo-600 hover:text-indigo-700"
        >
          Refresh
        </button>
      </div>

      <div v-if="loading" class="px-4 py-8 text-sm text-slate-500 text-center">
        Loading notifications...
      </div>

      <div v-else-if="error" class="px-4 py-6 text-sm text-red-600 text-center">
        {{ error }}
      </div>

      <div v-else-if="notifications.length === 0" class="px-4 py-8 text-sm text-slate-500 text-center">
        No notifications yet.
      </div>

      <div v-else class="max-h-96 overflow-y-auto divide-y divide-slate-100">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="px-4 py-4 cursor-pointer transition-colors"
          :class="notification.read_at ? 'bg-white hover:bg-slate-50' : 'bg-indigo-50/40 hover:bg-indigo-50'"
          @click="openNotification(notification)"
        >
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="text-sm font-semibold text-slate-900">
                {{ notification.message }}
              </p>
              <p
                v-if="notification.data?.event_title"
                class="mt-1 text-xs text-slate-500"
              >
                Event: {{ notification.data.event_title }}
              </p>
              <p class="mt-2 text-xs text-slate-400">
                {{ formatDate(notification.created_at) }}
              </p>
            </div>

            <button
              v-if="!notification.read_at"
              @click.stop="markAsRead(notification)"
              class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-700"
            >
              Mark read
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import api from "../api/axios";

const router = useRouter();
const rootEl = ref(null);
const isOpen = ref(false);
const loading = ref(false);
const error = ref("");
const notifications = ref([]);

const unreadCount = computed(
  () => notifications.value.filter((notification) => !notification.read_at).length
);

const formatDate = (dateString) => {
  if (!dateString) return "";

  return new Date(dateString).toLocaleString("en-US", {
    dateStyle: "medium",
    timeStyle: "short",
  });
};

const fetchNotifications = async () => {
  loading.value = true;
  error.value = "";

  try {
    const res = await api.get("/notifications");
    notifications.value = res.data?.data ?? [];
  } catch (err) {
    error.value = err.response?.data?.message || "Unable to load notifications.";
  } finally {
    loading.value = false;
  }
};

const togglePanel = async () => {
  isOpen.value = !isOpen.value;

  if (isOpen.value) {
    await fetchNotifications();
  }
};

const markAsRead = async (notification) => {
  try {
    const res = await api.patch(`/notifications/${notification.id}/read`);
    const updatedNotification = res.data?.data;

    notifications.value = notifications.value.map((item) =>
      item.id === notification.id ? { ...item, ...updatedNotification } : item
    );

    return updatedNotification;
  } catch (err) {
    error.value = err.response?.data?.message || "Unable to mark notification as read.";
    return null;
  }
};

const openNotification = async (notification) => {
  const updatedNotification = await markAsRead(notification);

  if (!updatedNotification && !notification.read_at) {
    return;
  }

  isOpen.value = false;

  const eventId = updatedNotification?.data?.event_id ?? notification.data?.event_id;

  if (eventId) {
    router.push(`/events/${eventId}`);
  }
};

const handleClickOutside = (event) => {
  if (rootEl.value && !rootEl.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>
