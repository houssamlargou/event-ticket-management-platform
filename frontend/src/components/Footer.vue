<template>
  <footer class="border-t border-slate-200 bg-white mt-12">
    <div class="max-w-7xl mx-auto px-6 py-10">
      <div class="flex flex-col gap-8 md:flex-row md:items-start md:justify-between">
        <div class="max-w-md">
          <h2 class="text-lg font-black tracking-tight text-slate-900">ETM Platform</h2>
          <p class="mt-2 text-sm text-slate-500">
            Event &amp; Ticket Management Platform
          </p>
        </div>

        <nav class="flex flex-wrap gap-x-6 gap-y-3">
          <RouterLink
            v-for="item in footerLinks"
            :key="item.label"
            :to="item.to"
            class="text-sm font-semibold text-slate-600 transition-colors hover:text-indigo-600"
          >
            {{ item.label }}
          </RouterLink>
        </nav>
      </div>

      <div class="mt-8 border-t border-slate-100 pt-6 text-sm text-slate-400">
        © {{ currentYear }} ETM Platform. All rights reserved.
      </div>
    </div>
  </footer>
</template>

<script setup>
import { RouterLink } from "vue-router";

const currentYear = new Date().getFullYear();

const footerLinks = [
  { label: "Home", to: "/" },
  { label: "Events", to: "/" },
  { label: "Favorites", to: "/favorites" },
  { label: "My Orders", to: "/orders" },
];
</script>
