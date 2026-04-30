<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <Navbar />
    <main class="flex-1">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Footer from "./components/Footer.vue";
import Navbar from "./components/Navbar.vue";
</script>
