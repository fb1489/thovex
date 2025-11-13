import axios from '../Lib/axios';
import MapMarker from '../Lib/MapMarker';

export default class Map {
    private map!: google.maps.Map;
    private addMapMarkerListener: google.maps.MapsEventListener|null = null;
    private mapMarkers: Array<google.maps.marker.AdvancedMarkerElement> = [];
    private mapMarkerListeners: Array<CallableFunction> = [];

    async init(
        GmpMapElement: HTMLElement,
        startingLatitudeAndLongitude: google.maps.LatLng,
        startingZoomLevel: number = 10,
        mapId: string = "TECH_TEST_MAP",
    ) {
        const { Map: GoogleMap } = await google.maps.importLibrary("maps") as google.maps.MapsLibrary;

        this.map = new GoogleMap(GmpMapElement, {
            center: startingLatitudeAndLongitude,
            zoom: startingZoomLevel,
            mapId: mapId,
        });
    }

    isAddingMapMarkerOnMapClickEnabled(): boolean {
        return this.addMapMarkerListener !== null;
    }

    addMapMarkerOnMapClickToggle(): boolean {
        if (this.isAddingMapMarkerOnMapClickEnabled()) {
            this.disableAddingMapMarkerOnMapClick();
        } else {
            this.enableAddingMapMarkerOnMapClick();
        }

        return this.isAddingMapMarkerOnMapClickEnabled();
    }

    enableAddingMapMarkerOnMapClick(): void {
        if (this.addMapMarkerListener === null) {
            this.addMapMarkerListener = google.maps.event.addListener(this.map, 'click', this.createMapMarkerOnMapClick);
        }
    }

    disableAddingMapMarkerOnMapClick(): void {
        if (this.addMapMarkerListener) {
            google.maps.event.removeListener(this.addMapMarkerListener);
            this.addMapMarkerListener = null;
        }
    }

    createMapMarkers(mapMarkers: Array<MapMarker>): void {
        for (let mapMarker of mapMarkers) {
            this.addMarkerOn(new google.maps.LatLng(
                mapMarker.latitude,
                mapMarker.longitude
            ));
        }
    }

    addListenerForMapMarker(listener: CallableFunction): void {
        this.mapMarkerListeners.push(listener);
    }

    removeAllMapMarkers(): void {
        for (let mapMarker of this.mapMarkers) {
            mapMarker.remove();
        }
        this.mapMarkers = [];
        
        axios.delete('maps/remove_all_markers')
            .then((response) => {
                // noop
            })
            .catch((error) => {
                // todo error handling
            });
    }

    private addMarkerOn = (latLng: google.maps.LatLng): google.maps.marker.AdvancedMarkerElement => {
        let marker = new google.maps.marker.AdvancedMarkerElement({
            map: this.map,
            position: latLng,
        });
        
        this.mapMarkers.push(marker);

        for (let listener of this.mapMarkerListeners) {
            listener(marker);
        }

        return marker;
    }
    
    private createMapMarkerOnMapClick = (event: google.maps.MapMouseEvent): void => {
        if (event.latLng) {
            let newMarker = this.addMarkerOn(event.latLng);
            
            axios.post('maps/save_marker', {
                latitude: newMarker.position!.lat,
                longitude: newMarker.position!.lng,
            })
            .then((response) => {
                // noop
            })
            .catch((error) => {
                // todo error handling
            });
        }
  }
}