<?php
/**
 * Map Section Template
 *
 * Displays a Google Maps store locator for finding mutual banks.
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
<div class="fire-container py-16 lg:py-24" x-data="mapComponent(<?php echo esc_attr(wp_json_encode($locations)); ?>)">
  <?php if ($title): ?>
    <div class="mb-10 lg:mb-14">
      <?php new Fire_Heading($tag ? $tag : 'h2', $title, 'heading-2 text-white'); ?>
    </div>
  <?php endif; ?>

  <!-- Google Maps Extended Component Library -->
  <script type="module" src="https://unpkg.com/@googlemaps/extended-component-library@0.6"></script>

  <gmpx-api-loader key="AIzaSyCxJNkv4rDmOUL4_BtQ3YeA3cy5QbBYPdU" solution-channel="GMP_QB_locatorplus_v10_cABCDE"></gmpx-api-loader>
  <gmpx-store-locator map-id="2fed55d88c91c845" class="w-full min-h-[600px]"></gmpx-store-locator>

  <script type="module">
    // Initialize map after all elements are ready
    document.addEventListener('DOMContentLoaded', async () => {
      await customElements.whenDefined('gmpx-store-locator');
      const locator = document.querySelector('gmpx-store-locator');

      // Get the Alpine component instance to access configuration
      const mapContainer = document.querySelector('[x-data*="mapComponent"]');
      if (mapContainer && mapContainer._x_dataStack && locator) {
        const component = mapContainer._x_dataStack[0];
        if (component && component.configuration) {
          locator.configureFromQuickBuilder(component.configuration);
        }
      }
    });
  </script>
</div>
<?php $section->end(); ?>
