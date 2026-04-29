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
              v-if="isOrganizer"
              @click="$router.push(`/edit-event/${$route.params.id}`)"
              class="bg-white text-slate-900 px-6 py-2 rounded-lg font-bold text-sm shadow-md"
            >
              Edit Event
            </button>
            
            <button 
              v-if="isOrganizer"
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
      
      <div class="bg-white rounded-2xl p-8 shadow-lg mb-8 border border-slate-100">
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
              v-if="isNormalUser"
              @click.stop="toggleFavorite"
              class="border-2 border-slate-200 text-slate-700 px-6 py-4 rounded-xl font-bold flex items-center gap-2 shadow-sm"
            >
              {{ event.is_favorited ? '❤️ Favorited' : '🤍 Add to favorites' }}
            </button>
            <button
              v-if="isNormalUser"
              @click="scrollToTickets"
              class="bg-indigo-600 text-white px-10 py-4 rounded-xl font-bold"
            >
              Get Tickets
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
          
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-4 uppercase text-xs tracking-widest text-indigo-600">The Experience</h2>
            <p class="text-slate-600 text-lg whitespace-pre-line leading-relaxed font-medium">
              {{ event.description }}
            </p>
          </div>

          <section id="tickets" ref="ticketsSection" class="space-y-4 scroll-mt-24">
            <h2 class="text-xl font-bold text-slate-900 ml-2">Available Passes</h2>

            <div v-if="tickets.length === 0" class="bg-white p-10 rounded-2xl border-2 border-dashed border-slate-200 text-center text-slate-500 font-medium">
              No tickets available for this experience yet.
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="ticket in tickets"
                :key="ticket.id"
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 transition-colors"
              >
                <div class="flex-grow">
                  <h3 class="font-bold text-2xl text-slate-900 mb-1">{{ ticket.name }}</h3>
                  <div class="flex items-center gap-4">
                    <span class="text-indigo-600 font-bold text-xl">{{ ticket.price }} MAD</span>
                    <span class="text-slate-400 text-sm bg-slate-50 px-2 py-0.5 rounded border">
                      {{ ticket.quantity }} left
                    </span>
                  </div>
                </div>

                <div class="flex items-center gap-4 border-t md:border-t-0 pt-4 md:pt-0">
                  <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Qty</label>
                    <input
                      type="number"
                      min="1"
                      v-model.number="ticket.qty"
                      class="border border-slate-200 rounded-lg px-3 py-2 w-20 font-bold outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="1"
                    />
                  </div>
                  
                  <button
                    @click="buyTicket(ticket)"
                    class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold shadow-md hover:bg-emerald-700 transition self-end h-[46px]"
                  >
                    Buy Now
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 sticky top-8">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Information</h2>
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
              This is a verified experience. For group bookings or specific accessibility requests, please contact the host.
            </p>
            <button class="w-full border-2 border-slate-200 py-3 rounded-xl font-bold text-slate-700 hover:bg-slate-50 transition">
              Contact Host
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from "../api/axios";

const route = useRoute();
const router = useRouter();
const event = ref({});
const tickets = ref([]);
const currentUser = ref(JSON.parse(localStorage.getItem("user")));
const ticketsSection = ref(null);
const userRole = computed(() => currentUser.value?.user?.role ?? currentUser.value?.role ?? null);
const isOrganizer = computed(() => userRole.value === 'organizer');
const isNormalUser = computed(() => userRole.value === 'user');

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

const buyTicket = async (ticket) => {
  if (!ticket.qty || ticket.qty <= 0) {
    alert("Enter valid quantity");
    return;
  }

  try {
    await api.post("/orders", {
      ticket_id: ticket.id,
      quantity: ticket.qty,
    });

    alert("Order created successfully");

    const res = await api.get(`/events/${event.value.id}/tickets`);
    tickets.value = res.data.data;
  } catch (err) {
    console.error(err);
    alert("Error buying ticket");
  }
};

const scrollToTickets = async () => {
  await nextTick();

  const target = ticketsSection.value || document.getElementById("tickets");

  if (!target) return;

  const top = target.getBoundingClientRect().top + window.scrollY - 24;

  window.scrollTo({
    top: Math.max(top, 0),
    behavior: 'smooth',
  });
};
</script>
