<template>
  <div id="map-container">
    <div id="map-controls">
      <button @click="addMapMarkerOnMapClickToggle">
        {{ isAddingMapMarkerOnMapClickEnabled ? 'Disable' : 'Enable' }} Map Markers
      </button>
      <button v-if="hasMarkers" @click="removeAllMapMarkers">
        Remove All Map Markers
      </button>
      <button v-if="hasMarkers" @click="toggleMarkersList" class="align-right">
        {{ showMarkersList ? 'Hide' : 'Show' }} markers list
      </button>
    </div>

    <existing-markers v-if="hasMarkers && showMarkersList" id="existing-map-markers" :map="map"></existing-markers>

    <div id="map" ref="mapElement" :class="mapClasses"></div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted } from 'vue';
  import alertify from 'alertify.js';
  import Map from '../Lib/map';
  import MapMarker from '../Lib/MapMarker';
  import ExistingMarkers from './ExistingMarkers.vue';

  const props = defineProps({
    mapMarkers: {
      type: Array<MapMarker>,
      required: true,
    },
  });

  const mapElement = ref<HTMLElement>();
  const isAddingMapMarkerOnMapClickEnabled = ref<boolean>(false);
  const hasMarkers = ref<boolean>(props.mapMarkers.length > 0);
  const showMarkersList = ref<boolean>(false);

  const map = new Map();

  const mapClasses = computed(() => {return {
    'side-bar-open': showMarkersList.value,
  }});

  function addMapMarkerOnMapClickToggle() {
    isAddingMapMarkerOnMapClickEnabled.value = map.addMapMarkerOnMapClickToggle();
  }

  function removeAllMapMarkers() {
    alertify.confirm(
      "Are you sure you want to remove all markers? This action is irreversible.",
      (/* on accept */) => {
        map.removeAllMapMarkers();
        hasMarkers.value = false;
        showMarkersList.value = false;
        alertify.log('All markers were removed');
      }
    );
  }

  function toggleMarkersList() {
    showMarkersList.value = !showMarkersList.value;
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
#map.side-bar-open {
  right: 275px;
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
#map-controls button.align-right {
  margin-left: auto;
}
#existing-map-markers {
  position: absolute;
  top: 50px;
  right: 0;
  bottom: 0;
  width: 275px;
  background: #FFF;
  border: solid 2px #000;
  border-left: none;
  overflow-y: auto;
}
</style>
