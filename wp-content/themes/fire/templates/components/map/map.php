<?php
/**
 * Map Section Template
 *
 * Displays an OpenStreetMap store locator for finding mutual banks.
 *
 * @package Fire
 */

// Retrieve ACF fields for this section
$tag = get_sub_field('tag');
$title = get_sub_field('title');

// Get locations from custom post type
$locations = fire_get_locations_for_map();

// Add custom classes to the section wrapper
$section->add_classes([
  'map'
]);
?>

<?php $section->start(); ?>

<div class="fire-container py-16 lg:py-24">
  <?php if ($title): ?>
    <div class="mb-10 lg:mb-14">
      <?php new Fire_Heading($tag ? $tag : 'h2', $title, 'heading-2 text-white'); ?>
    </div>
  <?php endif; ?>

  <!-- Map locator in full-width wrapper for desktop side-by-side layout -->
  <div class="full-width-no-subgrid">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />

    <div id="map-container">
      <!-- Locations Sidebar -->
      <div id="locations-sidebar">
        <div class="search-header">
          <h3>Find a location near you</h3>
          <div class="search-input-wrapper">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input
              type="text"
              id="location-search"
              class="search-input"
              placeholder="Enter your address or zip code"
              autocomplete="off"
            />
          </div>
        </div>

        <div class="locations-count" id="locations-count">
          All locations (<?php echo count($locations); ?>)
        </div>

        <div id="locations-list"></div>
      </div>

      <!-- Map -->
      <div id="map"></div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
      crossorigin=""></script>

    <script>
      // Location data from WordPress
      const LOCATIONS = <?php echo wp_json_encode($locations, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

      // Font Awesome map pin SVG
      const MAP_PIN_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
        <path d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z"/>
      </svg>`;

      // Initialize the map
      let map;
      let markers = [];
      let activeMarker = null;
      let userMarker = null;

      // Initialize map centered on US
      function initMap() {
        map = L.map('map').setView([39.8283, -98.5795], 4);

        // Add CartoDB Voyager tile layer (has darker blue water similar to Google Maps)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
          maxZoom: 19
        }).addTo(map);

        // Add markers for all locations
        addLocationMarkers();

        // Render location list
        renderLocationList(LOCATIONS);
      }

      // Create Font Awesome style marker icon
      function createMarkerIcon(isActive = false) {
        return L.divIcon({
          className: 'marker-pin' + (isActive ? ' active' : ''),
          html: MAP_PIN_SVG,
          iconSize: [40, 40],
          iconAnchor: [20, 40],
          popupAnchor: [0, -40]
        });
      }

      // Add markers for all locations
      function addLocationMarkers() {
        LOCATIONS.forEach((location, index) => {
          if (location.coords && location.coords.lat && location.coords.lng) {
            const marker = L.marker([location.coords.lat, location.coords.lng], {
              icon: createMarkerIcon()
            });

            // Create popup content
            const popupContent = `
              <div class="popup-title">${location.title}</div>
              <div class="popup-address">
                ${location.address1 ? location.address1 + '<br>' : ''}
                ${location.address2 || ''}
              </div>
              <div class="popup-actions">
                ${location.actions && location.actions.length > 0 ?
                  location.actions.map(action =>
                    `<a href="${action.defaultUrl}" target="_blank" class="location-action popup-action">
                      ${action.label}
                    </a>`
                  ).join('') : ''}
                <a href="https://www.google.com/maps/dir/?api=1&destination=${location.coords.lat},${location.coords.lng}" target="_blank" class="location-action popup-action">
                  Directions
                </a>
              </div>
            `;

            marker.bindPopup(popupContent);
            marker.locationIndex = index;

            marker.on('click', function() {
              setActiveMarker(this);
              highlightLocationCard(index);
            });

            marker.addTo(map);
            markers.push(marker);
          }
        });
      }

      // Set active marker
      function setActiveMarker(marker) {
        // Reset previous active marker
        if (activeMarker) {
          activeMarker.setIcon(createMarkerIcon(false));
        }

        // Set new active marker
        activeMarker = marker;
        marker.setIcon(createMarkerIcon(true));
      }

      // Render location list
      function renderLocationList(locations) {
        const listContainer = document.getElementById('locations-list');
        listContainer.innerHTML = '';

        if (locations.length === 0) {
          listContainer.innerHTML = '<div class="no-results">No locations found matching your search.</div>';
          return;
        }

        locations.forEach((location, index) => {
          const card = document.createElement('div');
          card.className = 'location-card';
          card.dataset.index = index;

          card.innerHTML = `
            <div class="location-name">${location.title}</div>
            <div class="location-address">
              ${location.address1 ? location.address1 + '<br>' : ''}
              ${location.address2 || ''}
            </div>
            <div class="location-actions">
              ${location.actions && location.actions.length > 0 ?
                location.actions.map(action =>
                  `<a href="${action.defaultUrl}" target="_blank" class="location-action" onclick="event.stopPropagation()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor">
                      <path d="M368 64C359.2 64 352 71.2 352 80C352 88.8 359.2 96 368 96L521.4 96L260.7 356.7C254.5 362.9 254.5 373.1 260.7 379.3C266.9 385.5 277.1 385.5 283.3 379.3L544 118.6L544 272C544 280.8 551.2 288 560 288C568.8 288 576 280.8 576 272L576 80C576 71.2 568.8 64 560 64L368 64zM144 160C99.8 160 64 195.8 64 240L64 496C64 540.2 99.8 576 144 576L400 576C444.2 576 480 540.2 480 496L480 400C480 391.2 472.8 384 464 384C455.2 384 448 391.2 448 400L448 496C448 522.5 426.5 544 400 544L144 544C117.5 544 96 522.5 96 496L96 240C96 213.5 117.5 192 144 192L240 192C248.8 192 256 184.8 256 176C256 167.2 248.8 160 240 160L144 160z"/>
                    </svg>
                    ${action.label}
                  </a>`
                ).join('') : ''}
              <a href="https://www.google.com/maps/dir/?api=1&destination=${location.coords.lat},${location.coords.lng}" target="_blank" class="location-action" onclick="event.stopPropagation()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor">
                  <path d="M160 252.6C160 166.6 231.1 96 320 96C408.9 96 480 166.6 480 252.6C480 302.9 454.1 362 418.6 418.2C384.6 472 344.6 518.7 320 545.6C295.4 518.7 255.4 471.9 221.4 418.2C185.9 362 160 302.9 160 252.6zM320 64C214 64 128 148.4 128 252.6C128 371.9 248.2 514.9 298.4 569.4C310.2 582.2 329.8 582.2 341.6 569.4C391.8 514.9 512 371.9 512 252.6C512 148.4 426 64 320 64zM368 256C368 282.5 346.5 304 320 304C293.5 304 272 282.5 272 256C272 229.5 293.5 208 320 208C346.5 208 368 229.5 368 256zM320 176C275.8 176 240 211.8 240 256C240 300.2 275.8 336 320 336C364.2 336 400 300.2 400 256C400 211.8 364.2 176 320 176z"/>
                </svg>
                Directions
              </a>
            </div>
          `;

          card.addEventListener('click', () => {
            const marker = markers[index];
            if (marker) {
              map.setView([location.coords.lat, location.coords.lng], 12);
              marker.openPopup();
              setActiveMarker(marker);
              highlightLocationCard(index);
            }
          });

          listContainer.appendChild(card);
        });
      }

      // Highlight location card
      function highlightLocationCard(index) {
        document.querySelectorAll('.location-card').forEach(card => {
          card.classList.remove('active');
        });

        const activeCard = document.querySelector(`.location-card[data-index="${index}"]`);
        if (activeCard) {
          activeCard.classList.add('active');
          activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      }

      // Search functionality using Nominatim (OpenStreetMap geocoding)
      let searchTimeout;
      document.getElementById('location-search').addEventListener('input', function(e) {
        const query = e.target.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 3) {
          renderLocationList(LOCATIONS);
          document.getElementById('locations-count').textContent = `All locations (${LOCATIONS.length})`;
          return;
        }

        // Filter locations by title or address
        const filtered = LOCATIONS.filter(location => {
          const searchString = `${location.title} ${location.address1} ${location.address2}`.toLowerCase();
          return searchString.includes(query.toLowerCase());
        });

        renderLocationList(filtered);
        document.getElementById('locations-count').textContent = `${filtered.length} location${filtered.length !== 1 ? 's' : ''} found`;

        // Also search for the address using Nominatim
        searchTimeout = setTimeout(() => {
          searchAddress(query);
        }, 500);
      });

      // Search address using Nominatim
      function searchAddress(query) {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=us&limit=1`)
          .then(response => response.json())
          .then(data => {
            if (data && data.length > 0) {
              const result = data[0];
              const lat = parseFloat(result.lat);
              const lon = parseFloat(result.lon);

              // Remove previous user marker
              if (userMarker) {
                map.removeLayer(userMarker);
              }

              // Add marker for searched location (light-blue marker)
              userMarker = L.marker([lat, lon], {
                icon: L.divIcon({
                  className: 'marker-pin user-marker',
                  html: MAP_PIN_SVG,
                  iconSize: [40, 40],
                  iconAnchor: [20, 40],
                  popupAnchor: [0, -40]
                })
              }).addTo(map);

              userMarker.bindPopup(`<div class="popup-title">Your Location</div>`).openPopup();

              // Zoom to user location
              map.setView([lat, lon], 10);

              // Sort locations by distance
              const sorted = sortLocationsByDistance(lat, lon);
              renderLocationList(sorted);
              document.getElementById('locations-count').textContent = `${sorted.length} location${sorted.length !== 1 ? 's' : ''} (sorted by distance)`;
            }
          })
          .catch(error => {
            console.error('Geocoding error:', error);
          });
      }

      // Calculate distance between two points (Haversine formula)
      function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius of Earth in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
      }

      // Sort locations by distance from a point
      function sortLocationsByDistance(lat, lon) {
        return LOCATIONS.map(location => ({
          ...location,
          distance: calculateDistance(lat, lon, location.coords.lat, location.coords.lng)
        })).sort((a, b) => a.distance - b.distance);
      }

      // Initialize when DOM is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
      } else {
        initMap();
      }
    </script>
  </div>
</div>
<?php $section->end(); ?>
