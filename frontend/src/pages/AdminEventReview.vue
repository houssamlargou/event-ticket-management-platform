<template>
  <div class="min-h-screen bg-slate-50 py-12 px-6 font-sans">
    <div class="max-w-5xl mx-auto space-y-8">
      <button
        @click="router.push('/admin')"
        class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-600 transition"
      >
        ← Back to Dashboard
      </button>

      <div v-if="loading" class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
        <p class="text-slate-500 font-medium">Loading event review...</p>
      </div>

      <div v-else-if="error" class="bg-white border border-red-200 rounded-2xl p-8 text-center">
        <p class="text-red-600 font-medium">{{ error }}</p>
      </div>

      <div v-else class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="h-80 bg-slate-100">
          <img
            v-if="event.image"
            :src="event.image"
            class="w-full h-full object-cover"
          />
          <div
            v-else
            class="w-full h-full flex items-center justify-center text-slate-400 font-medium"
          >
            No image provided
          </div>
        </div>

        <div class="p-8 space-y-8">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div>
              <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                Admin Review
              </p>
              <h1 class="mt-2 text-4xl font-bold text-slate-900">
                {{ event.title }}
              </h1>
              <p class="mt-4 text-slate-600 whitespace-pre-line leading-relaxed">
                {{ event.description }}
              </p>
            </div>

            <span
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide self-start"
              :class="statusClasses(event.status)"
            >
              {{ event.status }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Organizer</p>
              <p class="mt-2 text-lg font-semibold text-slate-900">{{ event.user?.name || "Unknown" }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Location</p>
              <p class="mt-2 text-lg font-semibold text-slate-900">{{ event.location }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Date</p>
              <p class="mt-2 text-lg font-semibold text-slate-900">{{ formatDate(event.event_date) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
              <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Status</p>
              <p class="mt-2 text-lg font-semibold text-slate-900 capitalize">{{ event.status }}</p>
            </div>
          </div>

          <div v-if="actionError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-600 font-medium">
            {{ actionError }}
          </div>

          <div class="flex flex-wrap gap-3">
            <button
              @click="updateStatus('approved')"
              :disabled="submitting || deleting"
              class="bg-emerald-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition disabled:opacity-60"
            >
              {{ submitting && currentAction === "approved" ? "Approving..." : "Approve" }}
            </button>
            <button
              @click="updateStatus('rejected')"
              :disabled="submitting || deleting"
              class="bg-amber-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-amber-600 transition disabled:opacity-60"
            >
              {{ submitting && currentAction === "rejected" ? "Rejecting..." : "Reject" }}
            </button>
            <button
              @click="deleteEvent"
              :disabled="submitting || deleting"
              class="bg-red-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-red-700 transition disabled:opacity-60"
            >
              {{ deleting ? "Deleting..." : "Delete" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../api/axios";

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const submitting = ref(false);
const deleting = ref(false);
const currentAction = ref("");
const error = ref("");
const actionError = ref("");
const event = ref({});

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

const fetchEvent = async () => {
  loading.value = true;
  error.value = "";

  try {
    const res = await api.get(`/events/${route.params.id}`);
    event.value = res.data?.data ?? {};
  } catch (err) {
    if (handleAuthError(err.response?.status)) {
      return;
    }

    error.value = err.response?.data?.message || "Unable to load this event for review.";
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (status) => {
  submitting.value = true;
  currentAction.value = status;
  actionError.value = "";

  try {
    await api.patch(`/events/${route.params.id}/status`, { status });
    router.push("/admin");
  } catch (err) {
    if (handleAuthError(err.response?.status)) {
      return;
    }

    actionError.value = err.response?.data?.message || "Unable to update this event.";
  } finally {
    submitting.value = false;
    currentAction.value = "";
  }
};

const deleteEvent = async () => {
  const confirmed = window.confirm("Are you sure you want to delete this event?");
  if (!confirmed) return;

  deleting.value = true;
  actionError.value = "";

  try {
    await api.delete(`/events/${route.params.id}`);
    router.push("/admin");
  } catch (err) {
    if (handleAuthError(err.response?.status)) {
      return;
    }

    actionError.value = err.response?.data?.message || "Unable to delete this event.";
  } finally {
    deleting.value = false;
  }
};

onMounted(() => {
  fetchEvent();
});
</script>
