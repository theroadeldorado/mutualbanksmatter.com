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

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
      crossorigin=""></script>

    <div
      id="map-container"
      x-data="mapComponent()"
      x-init="locations = JSON.parse(atob('<?php echo base64_encode(wp_json_encode($locations)); ?>')); init()"
    >
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
              placeholder="Search by zip code"
              autocomplete="off"
              maxlength="5"
              @input="handleSearch($event)"
              @keydown="handleSearchKeydown($event)"
            />
          </div>
        </div>

        <div class="locations-count" id="locations-count">
          Enter a zip code to search
        </div>

        <div id="locations-list"></div>
      </div>

      <!-- Map -->
      <div id="map"></div>
    </div>
  </div>
</div>
<?php $section->end(); ?>
