<template>
  <div class="min-h-screen bg-slate-50 py-12 px-6 font-sans">
    <div class="max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">My Events</h1>
          <p class="text-slate-500 mt-2">
            Manage the events you created from one place.
          </p>
        </div>

        <button
          @click="$router.push('/create-event')"
          class="bg-indigo-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
        >
          Create Event
        </button>
      </header>

      <div v-if="loading" class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
        <p class="text-slate-500 font-medium">Loading your events...</p>
      </div>

      <div v-else-if="error" class="bg-white border border-red-200 rounded-2xl p-8 text-center">
        <p class="text-red-600 font-medium">{{ error }}</p>
      </div>

      <div
        v-else-if="events.length === 0"
        class="bg-white border border-dashed border-slate-300 rounded-2xl p-14 text-center"
      >
        <p class="text-slate-500 font-medium">You have not created any events yet.</p>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <article
          v-for="event in events"
          :key="event.id"
          class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm"
        >
          <div class="h-56 bg-slate-100">
            <img
              v-if="event.image"
              :src="event.image"
              class="w-full h-full object-cover"
            />
            <div
              v-else
              class="w-full h-full flex items-center justify-center text-slate-400 font-medium"
            >
              No image
            </div>
          </div>

          <div class="p-6 space-y-5">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-xl font-bold text-slate-900">{{ event.title }}</h2>
                <p class="text-sm text-slate-500 mt-1">
                  {{ formatDate(event.event_date) }}
                </p>
              </div>

              <span
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                :class="statusClasses(event.status)"
              >
                {{ event.status }}
              </span>
            </div>

            <div class="space-y-2 text-sm text-slate-600">
              <p><span class="font-semibold text-slate-700">Location:</span> {{ event.location }}</p>
              <p class="line-clamp-3">
                <span class="font-semibold text-slate-700">Description:</span>
                {{ event.description || 'No description provided.' }}
              </p>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
              <button
                @click="$router.push(`/events/${event.id}`)"
                class="border border-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50 transition"
              >
                View
              </button>
              <button
                @click="$router.push({ path: `/edit-event/${event.id}`, query: { redirect: '/organizer/events' } })"
                class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition"
              >
                Edit
              </button>
              <button
                @click="deleteEvent(event.id)"
                :disabled="deletingId === event.id"
                class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition disabled:opacity-60"
              >
                {{ deletingId === event.id ? "Deleting..." : "Delete" }}
              </button>
            </div>

            <section class="pt-5 border-t border-slate-100 space-y-4">
              <div>
                <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700">
                  Manage Tickets
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                  Add tickets for this event even while it is still pending approval.
                </p>
              </div>

              <form @submit.prevent="submitTicket(event.id)" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input
                  v-model="ticketForms[event.id].name"
                  type="text"
                  placeholder="Ticket name"
                  class="border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                <input
                  v-model.number="ticketForms[event.id].price"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="Price"
                  class="border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                <input
                  v-model.number="ticketForms[event.id].quantity"
                  type="number"
                  min="1"
                  placeholder="Quantity"
                  class="border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />

                <div class="sm:col-span-3 flex flex-col gap-3">
                  <button
                    type="submit"
                    :disabled="ticketSubmittingByEvent[event.id]"
                    class="self-start bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition disabled:opacity-60"
                  >
                    {{ ticketSubmittingByEvent[event.id] ? "Adding..." : "Add Ticket" }}
                  </button>

                  <p
                    v-if="ticketSuccessByEvent[event.id]"
                    class="text-sm text-emerald-600 font-medium"
                  >
                    {{ ticketSuccessByEvent[event.id] }}
                  </p>

                  <p
                    v-if="ticketErrorsByEvent[event.id]"
                    class="text-sm text-red-600 font-medium"
                  >
                    {{ ticketErrorsByEvent[event.id] }}
                  </p>
                </div>
              </form>

              <div class="space-y-3">
                <p v-if="ticketsLoadingByEvent[event.id]" class="text-sm text-slate-500">
                  Loading tickets...
                </p>

                <p
                  v-else-if="ticketsByEvent[event.id]?.length === 0"
                  class="text-sm text-slate-500"
                >
                  No tickets added yet.
                </p>

                <div v-else class="space-y-2">
                  <div
                    v-for="ticket in ticketsByEvent[event.id]"
                    :key="ticket.id"
                    class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
                  >
                    <div>
                      <p class="font-semibold text-slate-900">{{ ticket.name }}</p>
                      <p class="text-sm text-slate-500">
                        {{ ticket.price }} MAD
                      </p>
                    </div>
                    <span class="text-sm font-medium text-slate-600">
                      Qty: {{ ticket.quantity }}
                    </span>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import api from "../api/axios";

const router = useRouter();
const events = ref([]);
const loading = ref(false);
const error = ref("");
const deletingId = ref(null);
const ticketsByEvent = ref({});
const ticketsLoadingByEvent = ref({});
const ticketForms = ref({});
const ticketErrorsByEvent = ref({});
const ticketSuccessByEvent = ref({});
const ticketSubmittingByEvent = ref({});

const createTicketForm = () => ({
  name: "",
  price: "",
  quantity: "",
});

const ensureTicketState = (eventId) => {
  if (!ticketForms.value[eventId]) {
    ticketForms.value[eventId] = createTicketForm();
  }

  if (!ticketsByEvent.value[eventId]) {
    ticketsByEvent.value[eventId] = [];
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "";

  return new Date(dateString).toLocaleString("en-US", {
    dateStyle: "medium",
    timeStyle: "short",
  });
};

const statusClasses = (status) => {
  if (status === "approved") {
    return "bg-emerald-50 text-emerald-700";
  }

  if (status === "pending") {
    return "bg-amber-50 text-amber-700";
  }

  if (status === "rejected") {
    return "bg-red-50 text-red-700";
  }

  return "bg-slate-100 text-slate-600";
};

const fetchEvents = async () => {
  loading.value = true;
  error.value = "";

  try {
    const res = await api.get("/organizer/events");
    events.value = res.data?.data ?? [];
    events.value.forEach((event) => ensureTicketState(event.id));
    await Promise.all(events.value.map((event) => fetchTickets(event.id)));
  } catch (err) {
    if (err.response?.status === 401) {
      router.push("/login");
      return;
    }

    if (err.response?.status === 403) {
      router.push("/");
      return;
    }

    error.value = err.response?.data?.message || "Unable to load your events.";
  } finally {
    loading.value = false;
  }
};

const fetchTickets = async (eventId) => {
  ensureTicketState(eventId);
  ticketsLoadingByEvent.value[eventId] = true;

  try {
    const res = await api.get(`/events/${eventId}/tickets`);
    ticketsByEvent.value[eventId] = res.data?.data ?? [];
  } catch (err) {
    ticketErrorsByEvent.value[eventId] =
      err.response?.data?.message || "Unable to load tickets for this event.";
  } finally {
    ticketsLoadingByEvent.value[eventId] = false;
  }
};

const submitTicket = async (eventId) => {
  ensureTicketState(eventId);
  ticketErrorsByEvent.value[eventId] = "";
  ticketSuccessByEvent.value[eventId] = "";

  const form = ticketForms.value[eventId];
  const payload = {
    name: form.name?.trim() ?? "",
    price: form.price,
    quantity: form.quantity,
  };

  if (!payload.name || payload.price === "" || payload.quantity === "") {
    ticketErrorsByEvent.value[eventId] = "Name, price, and quantity are required.";
    return;
  }

  ticketSubmittingByEvent.value[eventId] = true;

  try {
    await api.post(`/events/${eventId}/tickets`, payload);
    ticketForms.value[eventId] = createTicketForm();
    ticketSuccessByEvent.value[eventId] = "Ticket created successfully.";
    await fetchTickets(eventId);
  } catch (err) {
    if (err.response?.status === 401) {
      router.push("/login");
      return;
    }

    if (err.response?.status === 403) {
      ticketErrorsByEvent.value[eventId] =
        err.response?.data?.message || "You can only add tickets to your own events.";
      return;
    }

    const validationErrors = err.response?.data?.errors;
    ticketErrorsByEvent.value[eventId] =
      validationErrors?.name?.[0] ||
      validationErrors?.price?.[0] ||
      validationErrors?.quantity?.[0] ||
      err.response?.data?.message ||
      "Unable to add ticket right now.";
  } finally {
    ticketSubmittingByEvent.value[eventId] = false;
  }
};

const deleteEvent = async (eventId) => {
  const confirmed = window.confirm("Are you sure you want to delete this event?");
  if (!confirmed) return;

  deletingId.value = eventId;

  try {
    await api.delete(`/events/${eventId}`);
    events.value = events.value.filter((event) => event.id !== eventId);
    delete ticketsByEvent.value[eventId];
    delete ticketsLoadingByEvent.value[eventId];
    delete ticketForms.value[eventId];
    delete ticketErrorsByEvent.value[eventId];
    delete ticketSuccessByEvent.value[eventId];
    delete ticketSubmittingByEvent.value[eventId];
  } catch (err) {
    if (err.response?.status === 401) {
      router.push("/login");
      return;
    }

    if (err.response?.status === 403) {
      router.push("/");
      return;
    }

    error.value = err.response?.data?.message || "Unable to delete this event.";
  } finally {
    deletingId.value = null;
  }
};

onMounted(() => {
  fetchEvents();
});
</script>
