<template>
  <div>
    <div v-for="marker in markers" class="marker">
      <div class="coordinates">
        {{ marker.position?.lat }} {{ marker.position?.lng }}
      </div>
      <button @click="map.goTo(marker)">Go</button>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import alertify from 'alertify.js';
  import Map from '../Lib/map';

  const props = defineProps({
    map: {
      type: Map,
      required: true,
    },
  });

  const map = props.map;
  const markers = ref<Array<google.maps.marker.AdvancedMarkerElement>>(map.getMarkers());

  onMounted(() => {
    map.addListenerForMapMarker(() => {
      markers.value = map.getMarkers().slice();
    });
  });
</script>

<style scoped>
.marker {
  padding: 5px;
  border-bottom: solid 1px #000;
  display: flex;
  align-items: center;
  padding: 15px;
}
.coordinates {
  font-family: monospace;
  overflow: hidden;
}
button {
  margin: 0;
  padding: 0 15px;
}
</style>
