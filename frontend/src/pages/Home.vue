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
          v-if="isOrganizer"
          @click="$router.push('/create-event')"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
        >
          Create Event
        </button>
      </header>

      <section class="bg-white border border-slate-200 rounded-2xl p-5 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
          <div class="flex-1">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
              City
            </label>
            <input
              v-model="filters.city"
              type="text"
              placeholder="e.g. Casablanca"
              class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div class="lg:w-56">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
              Time
            </label>
            <select
              v-model="filters.time"
              class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
            >
              <option value="">All</option>
              <option value="upcoming">Upcoming</option>
              <option value="today">Today</option>
              <option value="past">Past</option>
            </select>
          </div>

          <div class="flex flex-col sm:flex-row gap-3 lg:pb-px">
            <button
              @click="applyFilters"
              :disabled="loading"
              class="bg-indigo-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition disabled:opacity-60"
            >
              Apply
            </button>
            <button
              @click="clearFilters"
              :disabled="loading"
              class="border border-slate-300 text-slate-700 px-5 py-3 rounded-lg font-semibold hover:bg-slate-50 transition disabled:opacity-60"
            >
              Clear
            </button>
          </div>
        </div>
      </section>

      <div v-if="loading && events.length === 0" class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <p class="text-slate-500 font-medium">Loading events...</p>
      </div>

      <div v-else-if="events.length === 0" class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <p class="text-slate-500 font-medium">No events found.</p>
      </div>

      <div v-else class="space-y-10">
        <div class="relative">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" :class="{ 'opacity-60': loading }">
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

          <div
            v-if="loading"
            class="absolute inset-0 flex items-center justify-center pointer-events-none"
          >
            <div class="bg-white/95 border border-slate-200 rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 shadow-sm">
              Loading events...
            </div>
          </div>
        </div>

        <div v-if="pageNumbers.length > 1" class="flex flex-wrap justify-center gap-2">
          <button
            v-for="page in pageNumbers"
            :key="page"
            @click="goToPage(page)"
            :disabled="loading || page === currentPage"
            :class="[
              'min-w-10 h-10 px-3 rounded-lg border text-sm font-semibold transition-colors',
              page === currentPage
                ? 'bg-indigo-600 border-indigo-600 text-white'
                : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300 hover:text-indigo-600 disabled:opacity-60'
            ]"
          >
            {{ page }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, reactive, ref } from 'vue';
import api from "../api/axios";
import { getStoredUser, getUserRole } from "../utils/auth";

const events = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const currentUser = ref(getStoredUser());
const filters = reactive({
  city: "",
  time: "",
});
const isOrganizer = computed(() => getUserRole(currentUser.value) === 'organizer');
const pageNumbers = computed(() =>
  Array.from({ length: lastPage.value }, (_, index) => index + 1)
);

const formatMonth = (date) => new Date(date).toLocaleString('en-US', { month: 'short' });
const formatDay = (date) => new Date(date).getDate();

const buildEventQuery = (page = 1) => {
  const query = new URLSearchParams({
    page: String(page),
  });

  if (filters.city.trim()) {
    query.set("city", filters.city.trim());
  }

  if (filters.time) {
    query.set("time", filters.time);
  }

  return query.toString();
};

const fetchEvents = async (page = 1) => {
  loading.value = true;

  try {
    const res = await axios.get(`${api.defaults.baseURL}/events?${buildEventQuery(page)}`);
    events.value = res.data.data ?? [];
    currentPage.value = res.data.meta?.current_page ?? page;
    lastPage.value = res.data.meta?.last_page ?? 1;
  } catch (err) {
    console.error("Fetch error:", err);
  } finally {
    loading.value = false;
  }
};

const goToPage = async (page) => {
  if (page === currentPage.value || loading.value) return;
  await fetchEvents(page);
};

const applyFilters = async () => {
  await fetchEvents(1);
};

const clearFilters = async () => {
  filters.city = "";
  filters.time = "";
  await fetchEvents(1);
};

onMounted(() => {
  fetchEvents();
});
</script>
