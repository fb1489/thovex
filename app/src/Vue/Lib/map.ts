export default class Map {
    private map!: google.maps.Map;

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

    addClickEventListener(callable: (e: google.maps.MapMouseEvent) => void): google.maps.MapsEventListener {
        return google.maps.event.addListener(this.map, 'click', callable);
    }

    removeClickEventListener(listener: google.maps.MapsEventListener): void {
        google.maps.event.removeListener(listener);
    }

    addMarkerTo(latLng: google.maps.LatLng): google.maps.marker.AdvancedMarkerElement {
        return new google.maps.marker.AdvancedMarkerElement({
            map: this.map,
            position: latLng,
        });
    }
}