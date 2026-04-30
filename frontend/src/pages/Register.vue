<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Register</h1>
      <p class="text-gray-500 mb-6">Create your ETM account</p>

      <form @submit.prevent="register" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Name
          </label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Your full name"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Email
          </label>
          <input
            v-model="form.email"
            type="email"
            placeholder="you@example.com"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Password
          </label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Create a password"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Confirm Password
          </label>
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Repeat your password"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-60"
        >
          {{ loading ? "Creating account..." : "Register" }}
        </button>
      </form>

      <p v-if="error" class="mt-4 text-red-600 text-sm">
        {{ error }}
      </p>

      <p class="mt-6 text-sm text-gray-500">
        Already have an account?
        <RouterLink to="/login" class="font-semibold text-blue-600 hover:underline">
          Login
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { RouterLink, useRouter } from "vue-router";
import api from "../api/axios";
import { clearStoredAuth, extractAuthPayload, setStoredAuth } from "../utils/auth";

const router = useRouter();
const loading = ref(false);
const error = ref(null);

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const register = async () => {
  loading.value = true;
  error.value = null;

  try {
    const res = await api.post("/register", form);
    const { token, user } = extractAuthPayload(res.data);

    if (token && user) {
      setStoredAuth({ token, user });
      router.push("/");
      return;
    }

    clearStoredAuth();
    router.push("/login");
  } catch (err) {
    error.value =
      err.response?.data?.message ||
      Object.values(err.response?.data?.errors || {})?.flat()?.[0] ||
      "Unable to create your account right now.";
  } finally {
    loading.value = false;
  }
};
</script>
