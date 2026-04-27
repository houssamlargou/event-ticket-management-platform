
<template>
  <div>
    <h1>Events</h1>
    <ul>
      <li v-for="event in events" :key="event.id">
        {{ event.title }}
      </li>
    </ul>
  </div>
</template>

<script>
import api from "./api/axios";

export default {
  data() {
    return {
      events: [],
    };
  },
  async mounted() {
    try {
      const res = await api.get("/events");
      console.log(res.data);
      this.events = res.data.data;
    } catch (err) {
      console.error(err);
    }
  },
};
</script>
