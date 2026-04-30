<template>
  <div class="min-h-screen bg-slate-50 py-12 px-6 font-sans">
    <div class="max-w-7xl mx-auto space-y-8">
      <header>
        <h1 class="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
        <p class="text-slate-500 mt-2">
          Review platform activity and manage events and users from one place.
        </p>
      </header>

      <div v-if="loading" class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
        <p class="text-slate-500 font-medium">Loading dashboard...</p>
      </div>

      <div v-else class="space-y-8">
        <p v-if="error" class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-red-600 font-medium">
          {{ error }}
        </p>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-sm font-semibold text-slate-500">Total Events</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ totalEvents }}</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-sm font-semibold text-slate-500">Total Users</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ totalUsers }}</p>
          </div>
          <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-sm font-semibold text-slate-500">Pending Events</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ pendingEvents }}</p>
          </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-900">Events Management</h2>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 text-slate-600">
                <tr>
                  <th class="px-6 py-4 text-left font-semibold">Title</th>
                  <th class="px-6 py-4 text-left font-semibold">Organizer</th>
                  <th class="px-6 py-4 text-left font-semibold">Status</th>
                  <th class="px-6 py-4 text-left font-semibold">Date</th>
                  <th class="px-6 py-4 text-left font-semibold">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="events.length === 0">
                  <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                    No events found.
                  </td>
                </tr>
                <tr v-for="event in events" :key="event.id" class="align-top">
                  <td class="px-6 py-4 font-semibold text-slate-900">
                    <button
                      @click="openReview(event.id)"
                      class="text-left transition hover:underline"
                      :class="event.status === 'pending' ? 'text-indigo-700 hover:text-indigo-800' : 'text-slate-900 hover:text-indigo-700'"
                    >
                      {{ event.title }}
                    </button>
                  </td>
                  <td class="px-6 py-4 text-slate-600">{{ event.user?.name || 'Unknown' }}</td>
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                      :class="statusClasses(event.status)"
                    >
                      {{ event.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-slate-600">{{ formatDate(event.event_date) }}</td>
                  <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                      <button
                        @click="updateStatus(event.id, 'approved')"
                        :disabled="statusUpdatingId === event.id || deletingId === event.id"
                        class="bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-semibold hover:bg-emerald-700 transition disabled:opacity-60"
                      >
                        {{ statusUpdatingId === event.id ? 'Saving...' : 'Approve' }}
                      </button>
                      <button
                        @click="updateStatus(event.id, 'rejected')"
                        :disabled="statusUpdatingId === event.id || deletingId === event.id"
                        class="bg-amber-500 text-white px-3 py-2 rounded-lg text-xs font-semibold hover:bg-amber-600 transition disabled:opacity-60"
                      >
                        {{ statusUpdatingId === event.id ? 'Saving...' : 'Reject' }}
                      </button>
                      <button
                        @click="deleteEvent(event.id)"
                        :disabled="deletingId === event.id || statusUpdatingId === event.id"
                        class="bg-red-600 text-white px-3 py-2 rounded-lg text-xs font-semibold hover:bg-red-700 transition disabled:opacity-60"
                      >
                        {{ deletingId === event.id ? 'Deleting...' : 'Delete' }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-900">Users Management</h2>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 text-slate-600">
                <tr>
                  <th class="px-6 py-4 text-left font-semibold">Name</th>
                  <th class="px-6 py-4 text-left font-semibold">Email</th>
                  <th class="px-6 py-4 text-left font-semibold">Role</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="users.length === 0">
                  <td colspan="3" class="px-6 py-10 text-center text-slate-500">
                    No users found.
                  </td>
                </tr>
                <tr v-for="user in users" :key="user.id">
                  <td class="px-6 py-4 font-medium text-slate-900">{{ user.name }}</td>
                  <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
                  <td class="px-6 py-4 text-slate-600 capitalize">{{ user.role }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import api from "../api/axios";

const router = useRouter();
const loading = ref(false);
const error = ref("");
const events = ref([]);
const users = ref([]);
const statusUpdatingId = ref(null);
const deletingId = ref(null);

const totalEvents = computed(() => events.value.length);
const totalUsers = computed(() => users.value.length);
const pendingEvents = computed(
  () => events.value.filter((event) => event.status === "pending").length
);

const formatDate = (dateString) => {
  if (!dateString) return "";

  return new Date(dateString).toLocaleString("en-US", {
    dateStyle: "medium",
    timeStyle: "short",
  });
};

const statusClasses = (status) => {
  if (status === "approved") {
    return "bg-emerald-50 text-emerald-700";
  }

  if (status === "pending") {
    return "bg-amber-50 text-amber-700";
  }

  if (status === "rejected") {
    return "bg-red-50 text-red-700";
  }

  return "bg-slate-100 text-slate-600";
};

const handleAuthError = (status) => {
  if (status === 401) {
    router.push("/login");
    return true;
  }

  if (status === 403) {
    router.push("/");
    return true;
  }

  return false;
};

const fetchDashboardData = async () => {
  loading.value = true;
  error.value = "";

  try {
    const [eventsRes, usersRes] = await Promise.all([
      api.get("/admin/events"),
      api.get("/admin/users"),
    ]);

    events.value = eventsRes.data?.data ?? [];
    users.value = usersRes.data?.data ?? [];
  } catch (err) {
    if (handleAuthError(err.response?.status)) {
      return;
    }

    error.value = err.response?.data?.message || "Unable to load the admin dashboard.";
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (eventId, status) => {
  statusUpdatingId.value = eventId;
  error.value = "";

  try {
    await api.patch(`/events/${eventId}/status`, { status });
    events.value = events.value.map((event) =>
      event.id === eventId ? { ...event, status } : event
    );
  } catch (err) {
    if (handleAuthError(err.response?.status)) {
      return;
    }

    error.value = err.response?.data?.message || "Unable to update event status.";
  } finally {
    statusUpdatingId.value = null;
  }
};

const deleteEvent = async (eventId) => {
  const confirmed = window.confirm("Are you sure you want to delete this event?");
  if (!confirmed) return;

  deletingId.value = eventId;
  error.value = "";

  try {
    await api.delete(`/events/${eventId}`);
    events.value = events.value.filter((event) => event.id !== eventId);
  } catch (err) {
    if (handleAuthError(err.response?.status)) {
      return;
    }

    error.value = err.response?.data?.message || "Unable to delete this event.";
  } finally {
    deletingId.value = null;
  }
};

const openReview = (eventId) => {
  router.push(`/admin/events/${eventId}/review`);
};

onMounted(() => {
  fetchDashboardData();
});
</script>
