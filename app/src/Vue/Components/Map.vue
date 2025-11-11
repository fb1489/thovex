<template>
  <div id="container">
    <div id="map" ref="mapElement"></div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import Map from '../Lib/map';

  const mapElement = ref<HTMLElement>();

  const map = new Map();

  onMounted(() => {
    map.init(
      mapElement.value!,
      new google.maps.LatLng(51.520128225389065, -3.200732454703261)
    );
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
