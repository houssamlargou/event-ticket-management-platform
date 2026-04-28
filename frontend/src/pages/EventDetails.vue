<template>
  <div class="min-h-screen bg-[#f1f5f9] relative font-sans selection:bg-indigo-100">
    
    <nav class="max-w-7xl mx-auto px-6 py-8">
      <button 
        @click="$router.back()" 
        class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-colors font-bold text-sm group"
      >
        <span class="group-hover:-translate-x-1 transition-transform inline-block">←</span> 
        Back to Experiences
      </button>
    </nav>

    <main class="max-w-7xl mx-auto px-6 pb-24">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <div class="lg:col-span-4 lg:sticky lg:top-8 space-y-8">
          <div class="relative group shadow-2xl shadow-slate-300">
            <div class="relative rounded-[2.5rem] overflow-hidden bg-white border border-slate-200">
              <img 
                v-if="event.image" 
                :src="event.image" 
                class="w-full aspect-[3/4] object-cover transition-transform duration-700 group-hover:scale-105" 
              />
              <div v-else class="w-full aspect-[3/4] bg-slate-100 flex items-center justify-center italic text-slate-400">
                No Poster Available
              </div>
            </div>
          </div>

          <div class="p-8 rounded-[2rem] bg-white border border-slate-200 shadow-sm space-y-6">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl">📍</div>
              <div>
                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black mb-1">Venue</p>
                <p class="text-slate-900 font-bold leading-tight">{{ event.location }}</p>
              </div>
            </div>
            
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl">📅</div>
              <div>
                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black mb-1">Date & Time</p>
                <p class="text-slate-900 font-bold leading-tight">{{ formatDate(event.event_date) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-8 space-y-12">
          
          <header class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              Booking Live
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.95]">
              {{ event.title }}
            </h1>
          </header>

          <section>
            <h3 class="text-indigo-600 uppercase tracking-[0.3em] text-[10px] font-black mb-6">The Experience</h3>
            <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-200 shadow-sm">
              <p class="text-slate-600 text-lg md:text-xl leading-relaxed whitespace-pre-line font-medium">
                {{ event.description }}
              </p>
            </div>
          </section>

          <section class="space-y-6">
            <h3 class="text-indigo-600 uppercase tracking-[0.3em] text-[10px] font-black">Select Your Pass</h3>
            
            <div v-if="tickets.length === 0" class="p-16 rounded-[2.5rem] bg-slate-50 border-2 border-dashed border-slate-200 text-center text-slate-400 font-bold">
              Tickets aren't available for this experience yet.
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="ticket in tickets"
                :key="ticket.id"
                class="group bg-white border border-slate-200 rounded-[2rem] p-2 hover:border-indigo-500 hover:shadow-xl transition-all duration-500"
              >
                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-8">
                  <div>
                    <div class="flex items-center gap-3 mb-2">
                      <h4 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ ticket.name }}</h4>
                    </div>
                    <div class="inline-flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-widest">
                      <span class="text-emerald-500">●</span> 
                      {{ ticket.quantity }} Remaining
                    </div>
                  </div>

                  <div class="flex items-center gap-8">
                    <div class="text-right">
                      <span class="block text-3xl font-black text-slate-900 tracking-tighter">{{ ticket.price }}</span>
                      <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">MAD / Person</span>
                    </div>
                    <button class="bg-indigo-600 text-white h-16 px-10 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-100">
                      Claim
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <footer class="pt-8">
            <div class="p-8 rounded-[2rem] bg-slate-900 text-white flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
              <div class="relative z-10">
                <h4 class="font-bold text-lg">Have questions about this event?</h4>
                <p class="text-slate-400 text-sm">Our support team is available 24/7 for booking assistance.</p>
              </div>
              <button class="relative z-10 bg-white/10 hover:bg-white/20 px-6 py-3 rounded-xl text-sm font-bold transition-colors">
                Contact Organizer
              </button>
              <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
            </div>
          </footer>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
// Logic remains identical to your previous controller
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from "../api/axios";

const route = useRoute();
const event = ref({});
const tickets = ref([]);

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'short', month: 'long', day: 'numeric', year: 'numeric'
  });
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
</script>