<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Login</h1>
      <p class="text-gray-500 mb-6">Access your ETM account</p>

      <form @submit.prevent="login" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Email
          </label>
          <input
            v-model="email"
            type="email"
            placeholder="organizer@test.com"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Password
          </label>
          <input
            v-model="password"
            type="password"
            placeholder="password"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
        >
          Login
        </button>
      </form>

      <p v-if="error" class="mt-4 text-red-600 text-sm">
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script>
import api from "../api/axios";

export default {
  data() {
    return {
      email: "",
      password: "",
      error: null,
    };
  },

  methods: {
    async login() {
      try {
        const res = await api.post("/login", {
          email: this.email,
          password: this.password,
        });

        const token = res.data.data.token;
        localStorage.setItem("token", token);
        const userRes = await api.get('/user');
        localStorage.setItem("user", JSON.stringify(userRes.data.data));
        this.$router.push("/");
      } catch (err) {
        this.error = "Invalid email or password";
        console.error(err);
      }
    },
  },
};
</script>