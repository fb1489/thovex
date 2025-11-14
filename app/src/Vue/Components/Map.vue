<template>
  <div id="map-container">
    <div id="map-controls">
      <button @click="addMapMarkerOnMapClickToggle">
        {{ isAddingMapMarkerOnMapClickEnabled ? 'Disable' : 'Enable' }} Map Markers
      </button>
      <button v-if="hasMarkers" @click="removeAllMapMarkers">
        Remove All Map Markers
      </button>
    </div>
    <div id="map" ref="mapElement"></div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import alertify from 'alertify.js';
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
  const hasMarkers = ref<boolean>(props.mapMarkers.length > 0);

  const map = new Map();

  function addMapMarkerOnMapClickToggle() {
    isAddingMapMarkerOnMapClickEnabled.value = map.addMapMarkerOnMapClickToggle();
  }

  function removeAllMapMarkers() {
    alertify.confirm(
      "Are you sure you want to remove all markers? This action is irreversible.",
      (/* on accept */) => {
        map.removeAllMapMarkers();
        hasMarkers.value = false;
        alertify.log('All markers were removed');
      }
    );
  }

  onMounted(async () => {
    await map.init(
      mapElement.value!,
      new google.maps.LatLng(51.520128225389065, -3.200732454703261)
    );

    map.createMapMarkers(props.mapMarkers);
    map.addListenerForMapMarker(() => {
      hasMarkers.value = true;
      alertify.log('Marker created and saved');
    });
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
  gap: 20px
}
#map-controls button {
  margin: 0;
  min-width: 220px;
}
</style>
