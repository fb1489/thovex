import axios from '../Lib/axios';
import MapMarker from '../Lib/MapMarker';
import alertify from 'alertify.js';

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
            ), mapMarker.title);
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

    getMarkers(): Array<google.maps.marker.AdvancedMarkerElement> {
        return this.mapMarkers;
    }
    
    goTo(mapMarker: google.maps.marker.AdvancedMarkerElement): void {
        this.map.panTo(new google.maps.LatLng(
            mapMarker.position!.lat as number,
            mapMarker.position!.lng as number
        ));
    }

    private addMarkerOn = (latLng: google.maps.LatLng, title: string|null = null): google.maps.marker.AdvancedMarkerElement => {
        let marker = new google.maps.marker.AdvancedMarkerElement({
            map: this.map,
            position: latLng,
            title: title,
        });

        this.mapMarkers.push(marker);

        for (let listener of this.mapMarkerListeners) {
            listener(marker);
        }

        return marker;
    }
    
    private createMapMarkerOnMapClick = (event: google.maps.MapMouseEvent): void => {
        if (event.latLng) {

            let markerWithSameCoordinates = this.mapMarkers.find(
                (mapMarker: google.maps.marker.AdvancedMarkerElement) => {
                    return mapMarker.position!.lat === event.latLng?.lat()
                        && mapMarker.position!.lng === event.latLng?.lng();
                }
            );

            // we don't want to add duplicate markers
            if (markerWithSameCoordinates) {
                return;
            }

            alertify.prompt(
                "[Optional] Enter a title for your marker:",
                (inputValue) => {
                    this.createMapMarker(event.latLng!, inputValue);
                },
                () => {
                    this.createMapMarker(event.latLng!);
                },
            );
        }
    }

    private createMapMarker = (latLng: google.maps.LatLng, title: string|null = null): void => {
        let newMarker = this.addMarkerOn(latLng, title);
        
        axios.post('maps/save_marker', {
            latitude: newMarker.position!.lat,
            longitude: newMarker.position!.lng,
            title: title,
        })
        .then((response) => {
            // noop
        })
        .catch((error) => {
            // todo error handling
        });
    };
}