<template>
  <div class="min-h-screen bg-[#f1f5f9] py-16 px-6 font-sans">
    <div class="max-w-7xl mx-auto">
      
      <header class="flex flex-col md:flex-row md:items-center justify-between mb-16 gap-6">
        <div>
          <h1 class="text-5xl font-black text-slate-900 tracking-tight leading-tight">
            Find your next <br/>
            <span class="text-indigo-600">experience.</span>
          </h1>
        </div>
        
        <button
          @click="$router.push('/create-event')"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-bold transition-all active:scale-95 shadow-xl shadow-indigo-200"
        >
          + Host an Event
        </button>
      </header>

      <div v-if="events.length === 0" class="text-center py-20 bg-white rounded-3xl border border-slate-200">
        <p class="text-slate-500">No events found.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <article
          v-for="event in events"
          :key="event.id"
          @click="$router.push(`/events/${event.id}`)"
          class="group cursor-pointer bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden hover:shadow-2xl hover:shadow-slate-300 transition-all duration-500 flex flex-col h-full"
        >
          <div class="relative w-full pt-[75%] overflow-hidden bg-slate-100">
            <img
              v-if="event.image"
              :src="event.image"
              class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            />
            
            <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl border border-slate-200 text-center shadow-lg">
              <p class="text-[10px] font-black text-indigo-600 uppercase leading-none mb-1">{{ formatMonth(event.event_date) }}</p>
              <p class="text-xl font-black text-slate-900 leading-none">{{ formatDay(event.event_date) }}</p>
            </div>
          </div>

          <div class="p-10 flex-grow flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Registration Open</span>
              </div>

              <h2 class="text-2xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1 mb-3">
                {{ event.title }}
              </h2>
              
              <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-6">
                {{ event.description || 'Join us for an unforgettable experience at this premier event.' }}
              </p>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
              <div class="flex items-center text-slate-400 text-xs font-bold uppercase tracking-widest">
                <span class="mr-2 text-indigo-500 text-base">📍</span> {{ event.location }}
              </div>
              
              <div class="h-10 w-10 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-indigo-600 group-hover:rotate-45 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7" />
                </svg>
              </div>
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
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>