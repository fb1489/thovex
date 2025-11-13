import { createApp } from 'vue'
import myMapApp from './Components/Map.vue'

const el = document.getElementById('vue-map-app');
const mapMarkers = JSON.parse(el.dataset.mapMarkers || '[]');

createApp(myMapApp, { mapMarkers }).mount(el);
