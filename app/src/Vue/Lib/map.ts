export default class Map {
    async init(
        GmpMapElement: HTMLElement,
        startingLatitudeAndLongitude: google.maps.LatLng,
        startingZoomLevel: number = 10,
    ) {
        const { Map } = await google.maps.importLibrary("maps") as google.maps.MapsLibrary;

        let map = new Map(GmpMapElement, {
            center: startingLatitudeAndLongitude,
            zoom: startingZoomLevel,
        });
    }
}