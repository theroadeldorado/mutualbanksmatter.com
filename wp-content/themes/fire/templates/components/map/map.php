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
  'map py-16 lg:py-24'
]);
?>

<?php $section->start(); ?>
<div class="fire-container">
  <?php if ($title): ?>
    <div class="mb-6">
      <?php new Fire_Heading($tag ? $tag : 'h2', $title, 'heading-2 text-white'); ?>
    </div>
  <?php endif; ?>

  <!-- Google Maps Store Locator -->
  <div class="w-full min-h-[600px]">
    <!-- Please note unpkg.com is unaffiliated with Google Maps Platform. -->
    <script type="module" src="https://unpkg.com/@googlemaps/extended-component-library@0.6"></script>

    <!-- Uses components from the Extended Component Library; see
    https://github.com/googlemaps/extended-component-library for more information
    on these HTML tags and how to configure them. -->
    <gmpx-api-loader key="AIzaSyCxJNkv4rDmOUL4_BtQ3YeA3cy5QbBYPdU" solution-channel="GMP_QB_locatorplus_v10_cABCDE" version="beta"></gmpx-api-loader>
    <gmpx-store-locator map-id="2fed55d88c91c845" feature-set="intermediate"></gmpx-store-locator>
  </div>
</div>
<?php $section->end(); ?>

