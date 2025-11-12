<template>
  <div id="container">
    <div id="map" ref="mapElement"></div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import Map from '../Lib/map';

  const mapElement = ref<HTMLElement>();
  const markers = ref<Array<google.maps.marker.AdvancedMarkerElement>>([]);

  const map = new Map();
  
  function handleMapClick(event: google.maps.MapMouseEvent) {
    let newMarker = map.addMarkerTo(event.latLng!);
    markers.value.push(newMarker);
  }

  onMounted(async () => {
    await map.init(
      mapElement.value!,
      new google.maps.LatLng(51.520128225389065, -3.200732454703261)
    );

    map.addClickEventListener(handleMapClick);
  });
</script>

<style scoped>
#map {
  position: absolute;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;

  border: solid 2px #000;
  background: #CCC;
}
</style>
