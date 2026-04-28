<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center justify-between mb-10">
        <div>
          <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Upcoming Events</h1>
          <p class="text-slate-500 mt-2">Discover and book the best experiences near you.</p>
        </div>
      </div>

      <div v-if="events.length === 0" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-slate-300 mb-4 text-6xl">🗓️</div>
        <p class="text-slate-500 font-medium">Looking for events...</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="event in events"
          :key="event.id"
          @click="$router.push(`/events/${event.id}`)"
          class="group bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden cursor-pointer hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wide">
                Live Event
              </div>
              <div class="text-slate-400 group-hover:text-indigo-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </div>
            </div>

            <h2 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">
              {{ event.title }}
            </h2>

            <div class="space-y-2">
              <div class="flex items-center text-slate-500 text-sm">
                <span class="mr-2">📍</span>
                {{ event.location }}
              </div>
              <div class="flex items-center text-slate-500 text-sm italic">
                <span class="mr-2">📅</span>
                {{ formatDate(event.event_date) }}
              </div>
            </div>
          </div>

          <div class="h-1.5 w-full bg-slate-100 group-hover:bg-indigo-600 transition-colors"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from "../api/axios";

const events = ref([]);

// Simple date formatter to make the string look cleaner
const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'long',
    day: 'numeric',
  });
};

onMounted(async () => {
  try {
    const res = await api.get("/events");
    events.value = res.data.data;
  } catch (err) {
    console.error("Error loading events:", err);
  }
});
</script>