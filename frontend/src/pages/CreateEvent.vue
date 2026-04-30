<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create New Event</h1>
        <p class="text-slate-500 mt-2">Fill in the details to launch your next experience.</p>
      </div>

      <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <form @submit.prevent="submit" class="p-8 space-y-6">
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Event Title</label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                placeholder="e.g. Summer Music Festival"
              />
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none resize-none"
                placeholder="Tell people what to expect..."
              ></textarea>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Date & Time</label>
              <input
                v-model="form.event_date"
                type="datetime-local"
                required
                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-slate-600"
              />
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Location</label>
              <input
                v-model="form.location"
                type="text"
                required
                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                placeholder="Casablanca, Morocco"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Cover Image</label>
            <div 
              class="relative group border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-2xl p-4 transition-colors bg-slate-50/50"
            >
              <input
                type="file"
                accept="image/*"
                @change="handleFile"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
              />
              
              <div v-if="!imagePreview" class="text-center py-4">
                <div class="text-slate-400 text-3xl mb-2">📸</div>
                <p class="text-sm text-slate-500 font-medium">Click or drag to upload image</p>
                <p class="text-xs text-slate-400 mt-1">PNG, JPG up to 10MB</p>
              </div>

              <div v-else class="relative h-48 rounded-xl overflow-hidden shadow-inner">
                <img :src="imagePreview" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                  <span class="text-white text-xs font-bold px-3 py-1 bg-black/50 rounded-full backdrop-blur-sm">Change Image</span>
                </div>
              </div>
            </div>
          </div>

          <hr class="border-slate-100" />

          <div class="space-y-4">
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 disabled:opacity-50"
            >
              <span v-if="loading" class="animate-spin text-lg">🌀</span>
              {{ loading ? 'Creating Event...' : 'Launch Event' }}
            </button>

            <p v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg text-center font-medium border border-red-100">
              ⚠️ {{ error }}
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import api from "../api/axios";

const router = useRouter();
const loading = ref(false);
const error = ref(null);
const imagePreview = ref(null);

const form = reactive({
  title: "",
  description: "",
  event_date: "",
  location: "",
  image: null,
});

const handleFile = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.image = file;
    // Create local preview URL
    imagePreview.value = URL.createObjectURL(file);
  }
};

const submit = async () => {
  loading.value = true;
  error.value = null;

  try {
    const formData = new FormData();
    Object.keys(form).forEach(key => {
      if (form[key]) formData.append(key, form[key]);
    });

    await api.post("/events", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

      router.push("/organizer/events");
  } catch (err) {
    error.value = "Something went wrong. Please check your connection.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};
</script>
