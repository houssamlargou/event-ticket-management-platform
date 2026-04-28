<template>
  <div class="min-h-screen bg-slate-50 py-12 px-6">
    <div class="max-w-7xl mx-auto">
      <header class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div>
          <h1 class="text-4xl font-black text-slate-900 tracking-tight">Upcoming Experiences</h1>
          <p class="text-slate-500 text-lg mt-2">Handpicked events just for you.</p>
        </div>
        <div class="h-1 w-20 bg-indigo-600 rounded-full hidden md:block"></div>
      </header>

      <div v-if="events.length === 0" class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-slate-200">
        <div class="text-5xl mb-4">🎟️</div>
        <h3 class="text-xl font-bold text-slate-900">No events found</h3>
        <p class="text-slate-500">Check back later for new updates.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <article
          v-for="event in events"
          :key="event.id"
          @click="$router.push(`/events/${event.id}`)"
          class="group relative bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden cursor-pointer hover:shadow-2xl hover:-translate-y-2 transition-all duration-500"
        >
          <div class="relative h-60 overflow-hidden">
            <img
              v-if="event.image"
              :src="event.image"
              :alt="event.title"
              class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
            />
            <div v-else class="h-full w-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
              <span class="text-slate-400 font-medium">No Preview Available</span>
            </div>

            <div class="absolute top-4 right-4 bg-white/70 backdrop-blur-md px-3 py-2 rounded-2xl border border-white/50 text-center shadow-lg">
              <span class="block text-[10px] font-bold uppercase tracking-widest text-indigo-600 leading-none">
                {{ formatMonth(event.event_date) }}
              </span>
              <span class="block text-xl font-black text-slate-900">
                {{ formatDay(event.event_date) }}
              </span>
            </div>
          </div>

          <div class="p-6">
            <div class="flex items-center gap-2 mb-3">
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Available Now</span>
            </div>

            <h2 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors line-clamp-2 min-h-[3.5rem]">
              {{ event.title }}
            </h2>

            <div class="flex items-center text-slate-500 text-sm mt-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="truncate">{{ event.location }}</span>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from "../api/axios";

const events = ref([]);

// Date Formatting helpers
const formatMonth = (date) => new Date(date).toLocaleString('en-US', { month: 'short' });
const formatDay = (date) => new Date(date).getDate();

onMounted(async () => {
  try {
    const res = await api.get("/events");
    events.value = res.data.data;
  } catch (err) {
    console.error("Fetch error:", err);
  }
});
</script>

<style scoped>
/* Ensures titles don't break the card layout if they are too long */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>