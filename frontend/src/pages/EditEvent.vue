<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      
      <button 
        @click="$router.back()" 
        class="flex items-center text-slate-400 hover:text-indigo-600 transition-colors mb-6 text-sm font-medium group"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Event
      </button>

      <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
          <h1 class="text-2xl font-black text-slate-900">Edit Event</h1>
          <p class="text-slate-500 text-sm mt-1">Update your event information and media.</p>
        </div>

        <form @submit.prevent="submit" class="p-8 space-y-6">
          
          <div class="space-y-1">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Event Title</label>
            <input 
              v-model="title" 
              class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-700" 
              placeholder="Give it a catchy name"
            />
          </div>

          <div class="space-y-1">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Description</label>
            <textarea
              v-model="description"
              rows="4"
              class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-slate-600 resize-none"
              placeholder="What makes this event special?"
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Date & Time</label>
              <input
                v-model="event_date"
                type="datetime-local"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-slate-600"
              />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">Location</label>
              <input
                v-model="location"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-slate-600"
                placeholder="Where is it happening?"
              />
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 ml-1">
              Cover Image
            </label>

            <div
              class="relative group rounded-2xl overflow-hidden bg-slate-100 border-2 transition-all duration-300"
              :class="(previewImage || currentImage) ? 'border-transparent shadow-inner' : 'border-dashed border-slate-300 hover:border-indigo-400'"
            >
              <img
                v-if="previewImage || currentImage"
                :src="previewImage || currentImage"
                class="w-full h-64 object-cover"
              />

              <div
                v-else
                class="h-64 flex flex-col items-center justify-center text-center p-6"
              >
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-slate-400 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <p class="text-sm font-bold text-slate-600">Click to upload photo</p>
                <p class="text-xs text-slate-400 mt-1">High resolution landscape preferred</p>
              </div>

              <div
                class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                </svg>
                <span class="text-xs font-bold uppercase tracking-widest">Change Image</span>
              </div>

              <input
                type="file"
                accept="image/*"
                @change="handleFile"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
              />
            </div>
          </div>

          <div class="pt-4">
            <button class="w-full bg-slate-900 hover:bg-indigo-600 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-slate-200 active:scale-[0.98]">
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../api/axios";

export default {
  data() {
    return {
      title: "",
      description: "",
      event_date: "",
      location: "",
      image: null,
      currentImage: null,
      previewImage: null,
    };
  },

  async mounted() {
    const id = this.$route.params.id;
    const res = await api.get(`/events/${id}`);
    const event = res.data.data;

    this.title = event.title;
    this.description = event.description;
    this.event_date = event.event_date?.slice(0, 16);
    this.location = event.location;
    this.currentImage = event.image;
  },

  methods: {
    handleFile(e) {
      this.image = e.target.files[0];
      if (this.image) {
        this.previewImage = URL.createObjectURL(this.image);
      }
    },

    async submit() {
      const id = this.$route.params.id;

      const formData = new FormData();
      formData.append("title", this.title);
      formData.append("description", this.description);
      formData.append("event_date", this.event_date);
      formData.append("location", this.location);

      if (this.image) {
        formData.append("image", this.image);
      }

      await api.post(`/events/${id}?_method=PUT`, formData);

      this.$router.push(`/events/${id}`);
    },
  },
};
</script>