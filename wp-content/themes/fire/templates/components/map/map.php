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

// Add custom classes to the section wrapper
$section->add_classes([
  'map'
]);
?>

<?php $section->start(); ?>
<div class="fire-container py-16 lg:py-24" x-data="mapComponent()">
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
          console.log('Applying configuration from Alpine component...');
          console.log('Configuration:', {
            locationCount: component.configuration.locations.length,
            mapId: component.configuration.mapOptions.mapId,
            apiKey: component.configuration.mapsApiKey ? 'Set' : 'Missing'
          });
          locator.configureFromQuickBuilder(component.configuration);
          console.log('Configuration applied. Map should render now.');
        } else {
          console.error('Component or configuration not found', { component, hasConfig: !!component?.configuration });
        }
      } else {
        console.error('Required elements not found', { mapContainer: !!mapContainer, locator: !!locator });
      }
    });
  </script>
</div>
<?php $section->end(); ?>
