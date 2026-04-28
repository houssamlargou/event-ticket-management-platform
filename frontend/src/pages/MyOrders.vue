<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">My Orders</h1>
        <p class="text-slate-500 mt-2">Manage your tickets and upcoming events.</p>
      </header>

      <div v-if="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>

      <div v-else-if="orders.length === 0" class="text-center bg-white rounded-xl p-12 border border-dashed border-slate-300">
        <p class="text-slate-400">No orders found yet.</p>
      </div>

      <div v-else class="grid gap-6">
        <div
          v-for="order in orders"
          :key="order.id"
          class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
        >
          <div class="p-6">
            <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
              <div>
                <h2 class="text-xl font-bold text-slate-900">{{ order.ticket?.event?.title }}</h2>
                <p class="text-slate-500 text-sm mt-1">
                  {{ order.ticket?.name }} • {{ order.quantity }} ticket{{ order.quantity > 1 ? 's' : '' }}
                </p>
              </div>
              
              <span :class="statusClasses[order.payment_status]" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider border">
                {{ order.payment_status }}
              </span>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <div class="text-slate-900 font-semibold">
                <span class="text-slate-500 font-normal text-sm">Total:</span> 
                {{ order.total_price }} <span class="text-xs">MAD</span>
              </div>

              <div v-if="order.payment_status === 'pending'" class="flex gap-3">
                <button
                  @click="cancelOrder(order.id)"
                  class="text-sm font-medium text-slate-600 hover:text-red-600 transition-colors"
                >
                  Cancel
                </button>
                <button
                  @click="payOrder(order.id)"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm"
                >
                  Pay Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from "../api/axios";

const orders = ref([]);
const loading = ref(true);

// Modern badge styling map
const statusClasses = {
  pending: 'bg-amber-50 text-amber-700 border-amber-200',
  paid: 'bg-emerald-50 text-emerald-700 border-emerald-200',
  cancelled: 'bg-slate-50 text-slate-500 border-slate-200',
};

const fetchOrders = async () => {
  try {
    const res = await api.get("/orders");
    orders.value = res.data.data;
  } catch (err) {
    console.error("Failed to fetch orders:", err);
  } finally {
    loading.value = false;
  }
};

const payOrder = async (id) => {
  try {
    await api.post(`/orders/${id}/pay`);
    await fetchOrders();
  } catch (err) {
    console.error(err);
  }
};

const cancelOrder = async (id) => {
  if (!confirm('Are you sure you want to cancel this order?')) return;
  try {
    await api.post(`/orders/${id}/cancel`);
    await fetchOrders();
  } catch (err) {
    console.error(err);
  }
};

onMounted(fetchOrders);
</script>