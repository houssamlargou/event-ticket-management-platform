<template>
  <div class="min-h-screen bg-slate-50 py-12 px-6 font-sans">
    <div class="max-w-7xl mx-auto">
      
      <header class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
          <h1 class="text-4xl font-bold text-slate-900">
            Find your next <br/>
            <span class="text-indigo-600">experience</span>
          </h1>
        </div>
        
        <button
          v-if="currentUser && currentUser.user.role === 'organizer'"
          @click="$router.push('/create-event')"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
        >
          Create Event
        </button>
      </header>

      <div v-if="events.length === 0" class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <p class="text-slate-500 font-medium">No events found.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="event in events"
          :key="event.id"
          @click="$router.push(`/events/${event.id}`)"
          class="cursor-pointer bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm flex flex-col h-full"
        >
          <div class="relative h-64 bg-slate-100">
            <img
              v-if="event.image"
              :src="event.image"
              class="w-full h-full object-cover"
            />
            
            <div class="absolute top-4 left-4 bg-white px-3 py-2 rounded-xl text-center shadow-sm">
              <p class="text-xs font-bold text-indigo-600 uppercase">{{ formatMonth(event.event_date) }}</p>
              <p class="text-xl font-bold text-slate-900">{{ formatDay(event.event_date) }}</p>
            </div>
          </div>

          <div class="p-6 flex-grow flex flex-col justify-between">
            <div>
              <h2 class="text-xl font-bold text-slate-900 mb-2">
                {{ event.title }}
              </h2>
              
              <p class="text-slate-500 text-sm mb-6 line-clamp-2">
                {{ event.description || 'Join us for an unforgettable experience.' }}
              </p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <div class="flex items-center text-slate-500 text-sm font-medium">
                <span class="mr-2">📍</span> {{ event.location }}
              </div>
              
              <div class="text-indigo-600 font-bold text-sm flex items-center gap-1">
                View Details <span>→</span>
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
const currentUser = JSON.parse(localStorage.getItem("user"));

onMounted(async () => {
  try {
    const res = await api.get("/events");
    events.value = res.data.data;
  } catch (err) {
    console.error("Fetch error:", err);
  }
});
</script>
