<template>
  <div class="min-h-screen bg-slate-50 pb-20">
    <div class="relative h-[40vh] w-full overflow-hidden">
      <img
        v-if="event.image"
        :src="event.image"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full bg-gradient-to-r from-indigo-600 to-purple-700"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
      
      <div class="absolute bottom-0 left-0 right-0 p-8">
        <div class="max-w-5xl mx-auto">
          <span class="bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Upcoming Event
          </span>
          <h1 class="text-4xl md:text-5xl font-black text-white mt-4 tracking-tight">
            {{ event.title }}
          </h1>
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 mt-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <div class="lg:col-span-2 space-y-8">
          <section class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-xl font-bold text-slate-900 mb-4">About this event</h2>
            <p class="text-slate-600 leading-relaxed whitespace-pre-line">
              {{ event.description }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-8 pt-8 border-t border-slate-100">
              <div class="flex items-start gap-3">
                <div class="p-2 bg-slate-50 rounded-lg text-indigo-600">📍</div>
                <div>
                  <p class="text-sm font-bold text-slate-900">Location</p>
                  <p class="text-slate-500 text-sm">{{ event.location }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="p-2 bg-slate-50 rounded-lg text-indigo-600">📅</div>
                <div>
                  <p class="text-sm font-bold text-slate-900">Date & Time</p>
                  <p class="text-slate-500 text-sm">{{ formatDate(event.event_date) }}</p>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div class="space-y-6">
          <h2 class="text-2xl font-bold text-slate-900">Get Tickets</h2>
          
          <div v-if="tickets.length === 0" class="bg-slate-100 p-6 rounded-2xl text-center text-slate-500">
            No tickets available for this event.
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="ticket in tickets"
              :key="ticket.id"
              class="group bg-white p-6 rounded-2xl border-2 border-transparent hover:border-indigo-500 shadow-sm transition-all duration-200"
            >
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-slate-900">{{ ticket.name }}</h3>
                <span class="text-indigo-600 font-black text-lg">{{ ticket.price }} <small class="text-[10px] uppercase">MAD</small></span>
              </div>
              
              <div class="flex items-center justify-between mt-4">
                <span class="text-xs font-medium px-2 py-1 bg-emerald-50 text-emerald-700 rounded-md">
                  {{ ticket.quantity }} left
                </span>
                <button class="bg-slate-900 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-indigo-600 transition-colors">
                  Select
                </button>
              </div>
            </div>
          </div>

          <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100">
            <p class="text-indigo-900 text-sm font-bold mb-1">Need help?</p>
            <p class="text-indigo-700 text-xs">Contact the organizer for special accessibility requests or group bookings.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from "../api/axios";

const route = useRoute();
const event = ref({});
const tickets = ref([]);

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

onMounted(async () => {
  const id = route.params.id;
  try {
    // Fetch both simultaneously for speed
    const [eventRes, ticketsRes] = await Promise.all([
      api.get(`/events/${id}`),
      api.get(`/events/${id}/tickets`)
    ]);
    
    event.value = eventRes.data.data;
    tickets.value = ticketsRes.data.data;
  } catch (err) {
    console.error("Error loading event data:", err);
  }
});
</script>