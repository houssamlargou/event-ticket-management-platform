<template>
  <div class="min-h-screen bg-slate-50 py-12 px-6 font-sans">
    <div class="max-w-7xl mx-auto">
      
      <header class="mb-12">
        <h1 class="text-4xl font-bold text-slate-900">
          My <span class="text-indigo-600">Favorites</span>
        </h1>
        <p class="text-slate-500 mt-2">All the experiences you've saved in one place.</p>
      </header>

      <div v-if="loading" class="text-center py-20">
        <div class="text-slate-500 font-medium">Loading your favorites...</div>
      </div>

      <div v-else-if="events.length === 0" class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <p class="text-slate-500 font-medium">No favorites yet. Start exploring to add some!</p>
        <button 
          @click="$router.push('/events')"
          class="mt-4 text-indigo-600 font-bold hover:underline"
        >
          Browse Events →
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="event in events"
          :key="event.id"
          @click="$router.push(`/events/${event.id}`)"
          class="cursor-pointer bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm flex flex-col h-full"
        >
          <div class="relative h-48 bg-slate-100">
            <img
              v-if="event.image"
              :src="event.image"
              class="w-full h-full object-cover"
            />
            <div class="absolute top-3 right-3 bg-white/90 p-2 rounded-lg shadow-sm">
              ❤️
            </div>
          </div>

          <div class="p-6 flex-grow flex flex-col justify-between">
            <div>
              <h2 class="text-xl font-bold text-slate-900 mb-2 line-clamp-1">
                {{ event.title }}
              </h2>
              <div class="flex items-center text-slate-500 text-sm font-medium">
                <span class="mr-2">📍</span> {{ event.location }}
              </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center">
              <span class="text-indigo-600 font-bold text-sm">View Details</span>
              <span class="text-slate-400 text-xs uppercase font-bold tracking-wider">Saved</span>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../api/axios";

const events = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await api.get("/favorites");
    events.value = res.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>