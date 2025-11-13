<template>
  <div id="map-container">
    <div id="map-controls">
      <button @click="allowMarkerCreationToggle">
        {{ allowMarkerCreation ? 'Disable' : 'Enable' }} Map Markers
      </button>
    </div>
    <div id="map" ref="mapElement"></div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import Map from '../Lib/map';
  import axios from '../Lib/axios';
  import MapMarker from '../Lib/MapMarker';

  const props = defineProps({
    mapMarkers: {
      type: Array<MapMarker>,
      required: true,
    },
  });

  const mapElement = ref<HTMLElement>();
  const markers = ref<Array<google.maps.marker.AdvancedMarkerElement>>([]);
  const allowMarkerCreation = ref<boolean>(false);

  const map = new Map();
  let clickEventListener: google.maps.MapsEventListener|null = null;
  
  function handleMapClick(event: google.maps.MapMouseEvent) {
    let newMarker = map.addMarkerTo(event.latLng!);
    markers.value.push(newMarker);
    
    axios.post('maps/save_marker', {
      latitude: newMarker.position!.lat,
      longitude: newMarker.position!.lng,
    })
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
        // todo error handling
      })
  }

  function createInitialMapMarkers() {
    for (let mapMarker of props.mapMarkers) {
      map.addMarkerTo(new google.maps.LatLng(
        mapMarker.latitude,
        mapMarker.longitude
      ));
    }
  }

  function allowMarkerCreationToggle() {
    allowMarkerCreation.value = !allowMarkerCreation.value;

    if (allowMarkerCreation.value) {
      if (clickEventListener === null) {
        clickEventListener = map.addClickEventListener(handleMapClick);
      }
    } else if (clickEventListener) {
      map.removeClickEventListener(clickEventListener);
      clickEventListener = null;
    }
  }

  onMounted(async () => {
    await map.init(
      mapElement.value!,
      new google.maps.LatLng(51.520128225389065, -3.200732454703261)
    );

    createInitialMapMarkers();
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
}
</style>
