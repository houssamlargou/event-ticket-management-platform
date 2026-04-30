<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Profile</h1>
        <p class="text-slate-500 mt-2">Update your account information and password.</p>
      </header>

      <div v-if="loading" class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <p class="text-slate-500 font-medium">Loading profile...</p>
      </div>

      <div
        v-else-if="pageError"
        class="bg-white rounded-2xl border border-red-200 p-8 text-center"
      >
        <p class="text-red-600 font-medium">{{ pageError }}</p>
      </div>

      <div v-else class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-8 border-b border-slate-100">
            <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-bold mb-4">
              {{ profileInitial }}
            </div>
            <h2 class="text-2xl font-bold text-slate-900">{{ profile.name || "Unknown User" }}</h2>
            <p class="text-slate-500 mt-1">{{ profile.email || "No email available" }}</p>
          </div>

          <div class="p-8 grid gap-4">
            <div class="rounded-xl bg-slate-50 border border-slate-200 px-5 py-4">
              <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Email</p>
              <p class="text-slate-900 font-semibold">{{ profile.email || "-" }}</p>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 px-5 py-4">
              <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Role</p>
              <p class="text-slate-900 font-semibold capitalize">{{ roleLabel }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-8 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-900">Edit Profile</h3>
            <p class="text-slate-500 text-sm mt-1">Change your name and optionally update your password.</p>
          </div>

          <form @submit.prevent="updateProfile" class="p-8 space-y-5">
            <div v-if="successMessage" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
              {{ successMessage }}
            </div>

            <div v-if="formError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
              {{ formError }}
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
              <p v-if="validationErrors.name" class="mt-2 text-sm text-red-600">
                {{ validationErrors.name[0] }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Current Password</label>
              <input
                v-model="form.current_password"
                type="password"
                class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Required only when changing password"
              />
              <p v-if="validationErrors.current_password" class="mt-2 text-sm text-red-600">
                {{ validationErrors.current_password[0] }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">New Password</label>
              <input
                v-model="form.password"
                type="password"
                class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Leave blank to keep current password"
              />
              <p v-if="validationErrors.password" class="mt-2 text-sm text-red-600">
                {{ validationErrors.password[0] }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm New Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <button
              type="submit"
              :disabled="saving"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-semibold transition disabled:opacity-60"
            >
              {{ saving ? "Saving Changes..." : "Save Changes" }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import api from "../api/axios";
import { getStoredToken, getStoredUser, getUserRole, setStoredAuth } from "../utils/auth";

const loading = ref(true);
const saving = ref(false);
const pageError = ref(null);
const formError = ref(null);
const successMessage = ref("");
const validationErrors = ref({});
const currentUser = ref(getStoredUser());

const form = reactive({
  name: "",
  current_password: "",
  password: "",
  password_confirmation: "",
});

const profile = computed(() => currentUser.value?.user ?? currentUser.value ?? {});
const roleLabel = computed(() => getUserRole(currentUser.value) ?? "unknown");
const profileInitial = computed(() => {
  return profile.value?.name?.trim()?.charAt(0)?.toUpperCase() || "?";
});

const syncFormWithUser = () => {
  form.name = profile.value?.name ?? "";
  form.current_password = "";
  form.password = "";
  form.password_confirmation = "";
};

const fetchProfile = async () => {
  loading.value = true;
  pageError.value = null;

  try {
    const res = await api.get("/user");
    const user = res.data?.data?.user ?? res.data?.data ?? res.data?.user ?? res.data;

    currentUser.value = user;
    setStoredAuth({
      token: getStoredToken(),
      user,
    });
    syncFormWithUser();
  } catch (err) {
    pageError.value =
      err.response?.data?.message || "Unable to load your profile right now.";
  } finally {
    loading.value = false;
  }
};

const updateProfile = async () => {
  saving.value = true;
  formError.value = null;
  successMessage.value = "";
  validationErrors.value = {};

  try {
    const res = await api.put("/profile", {
      name: form.name,
      current_password: form.current_password,
      password: form.password,
      password_confirmation: form.password_confirmation,
    });

    const user = res.data?.data?.user ?? res.data?.data ?? res.data?.user ?? res.data;

    currentUser.value = user;
    setStoredAuth({
      token: getStoredToken(),
      user,
    });
    syncFormWithUser();
    successMessage.value = res.data?.message || "Profile updated successfully.";
  } catch (err) {
    validationErrors.value = err.response?.data?.errors || {};
    formError.value =
      err.response?.data?.message ||
      Object.values(validationErrors.value)?.flat?.()[0] ||
      "Unable to update your profile right now.";
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchProfile();
});
</script>
