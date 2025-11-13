<template>
  <div id="map-container">
    <div id="map-controls">
      <button @click="addMapMarkerOnMapClickToggle">
        {{ isAddingMapMarkerOnMapClickEnabled ? 'Disable' : 'Enable' }} Map Markers
      </button>
    </div>
    <div id="map" ref="mapElement"></div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted } from 'vue';
  import Map from '../Lib/map';
  import MapMarker from '../Lib/MapMarker';

  const props = defineProps({
    mapMarkers: {
      type: Array<MapMarker>,
      required: true,
    },
  });

  const mapElement = ref<HTMLElement>();
  const isAddingMapMarkerOnMapClickEnabled = ref<boolean>(false);

  const map = new Map();

  function addMapMarkerOnMapClickToggle() {
    isAddingMapMarkerOnMapClickEnabled.value = map.addMapMarkerOnMapClickToggle();
  }

  onMounted(async () => {
    await map.init(
      mapElement.value!,
      new google.maps.LatLng(51.520128225389065, -3.200732454703261)
    );

    map.createMapMarkers(props.mapMarkers);
  });
</script>

<style scoped>
#map {
  position: absolute;
  left: 0;
  top: 50px;
  right: 0;
  bottom: 0;

  border: solid 2px #000;
  background: #CCC;
}
#map-controls {
  position: absolute;
  left: 0;
  top: 0;
  right: 0;
  height: 50px;

  display: flex;
  align-items: flex-start;
}
#map-controls button {
  margin: 0;
  min-width: 220px;
}
</style>
