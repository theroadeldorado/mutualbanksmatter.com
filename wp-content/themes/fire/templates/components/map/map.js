export default (locationsData = []) => ({
  locations: [],
  configuration: null,

  init() {
    this.setupLocations(locationsData);
    this.setupConfiguration();
  },

  setupLocations(data) {
    this.locations = data;
    this.locations = this.locations.sort(this.dynamicSort('title'));
  },

  setupConfiguration() {
    this.configuration = {
      locations: [...this.locations],
      mapOptions: {
        center: { lat: 38.0, lng: -100.0 },
        fullscreenControl: true,
        mapTypeControl: false,
        streetViewControl: true,
        zoom: 4,
        zoomControl: true,
        maxZoom: 17,
        mapId: '2fed55d88c91c845',
      },
      mapsApiKey: 'AIzaSyCxJNkv4rDmOUL4_BtQ3YeA3cy5QbBYPdU',
      capabilities: {
        input: true,
        autocomplete: true,
        directions: false,
        distanceMatrix: true,
        details: true,
        actions: true,
      },
    };
  },

  dynamicSort(property) {
    let sortOrder = 1;
    if (property[0] === '-') {
      sortOrder = -1;
      property = property.substr(1);
    }
    return function (a, b) {
      const result = a[property] < b[property] ? -1 : a[property] > b[property] ? 1 : 0;
      return result * sortOrder;
    };
  },
});
