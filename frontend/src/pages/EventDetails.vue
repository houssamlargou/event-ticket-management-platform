<template>
  <div class="min-h-screen bg-slate-100 font-sans">
    
    <div class="relative w-full h-[50vh] bg-slate-900">
      <img 
        v-if="event.image" 
        :src="event.image" 
        class="w-full h-full object-cover" 
      />
      
      <nav class="absolute top-0 left-0 right-0 p-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
          <button @click="$router.back()" class="bg-white px-4 py-2 rounded-lg font-bold text-sm shadow-md">
            ← Back
          </button>
          
          <div class="flex gap-3">
            <button 
              v-if="currentUser && currentUser.user.role === 'organizer'"
              @click="$router.push(`/edit-event/${$route.params.id}`)"
              class="bg-white text-slate-900 px-6 py-2 rounded-lg font-bold text-sm shadow-md"
            >
              Edit Event
            </button>
            
            <button 
              v-if="currentUser && currentUser.user.role === 'organizer'"
              @click="deleteEvent" 
              class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold text-sm shadow-md"
            >
              Delete Event
            </button>
          </div>
        </div>
      </nav>
    </div>

    <main class="max-w-6xl mx-auto px-6 -mt-20 relative pb-20">
      
      <div class="bg-white rounded-2xl p-8 shadow-lg mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
          <div>
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">
              {{ event.title }}
            </h1>
            <div class="flex flex-wrap gap-6 text-slate-500 font-semibold">
              <p>📍 {{ event.location }}</p>
              <p>📅 {{ formatDate(event.event_date) }}</p>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <button
              v-if="currentUser && currentUser.user.role === 'user'"
              @click.stop="toggleFavorite"
              class="border-2 border-slate-200 text-slate-700 px-6 py-4 rounded-xl font-bold flex items-center gap-2 shadow-sm"
            >
              {{ event.is_favorited ? '❤️ Favorited' : '🤍 Add to favorites' }}
            </button>

            <button class="bg-indigo-600 text-white px-10 py-4 rounded-xl font-bold shadow-md">
              Get Tickets
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-4">The Experience</h2>
            <p class="text-slate-600 text-lg whitespace-pre-line leading-relaxed">
              {{ event.description }}
            </p>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Information</h2>
            <p class="text-slate-500 text-sm mb-6">
              This is a verified event. For more details or group bookings, contact the host.
            </p>
            <button class="w-full border-2 border-slate-200 py-3 rounded-xl font-bold text-slate-700">
              Contact Host
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from "../api/axios";

const route = useRoute();
const router = useRouter();
const event = ref({});
const tickets = ref([]);
const currentUser = ref(JSON.parse(localStorage.getItem("user")));

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
};

const deleteEvent = async () => {
  const confirmed = confirm("Are you sure you want to delete this event?");
  if (!confirmed) return;

  const id = route.params.id;

  await api.delete(`/events/${id}`);

  router.push("/");
};

onMounted(async () => {
  const id = route.params.id;
  try {
    const [eventRes, ticketsRes] = await Promise.all([
      api.get(`/events/${id}`),
      api.get(`/events/${id}/tickets`)
    ]);
    event.value = eventRes.data.data;
    tickets.value = ticketsRes.data.data;
  } catch (err) {
    console.error(err);
  }
});

const toggleFavorite = async () => {
  try {
    const res = await api.post(`/events/${event.value.id}/favorites`);
    event.value.is_favorited = res.data.favorited;
  } catch (err) {
    console.error(err);
  }
};
</script>