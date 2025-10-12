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
$form_id = get_sub_field('form');
$copy = get_sub_field('copy');

// Add custom classes to the section wrapper
$section->add_classes([
  'map py-16 lg:py-24'
]);
?>

<?php $section->start(); ?>
<div class="fire-container">
  <?php if ($title): ?>
    <div class="mb-10 lg:mb-14">
      <?php new Fire_Heading($tag ? $tag : 'h2', $title, 'heading-2 text-white'); ?>
    </div>
  <?php endif; ?>

  <?php if ($copy): ?>
    <div class="wizzy col-[main] md:col-[col-1/col-6] mb-10 lg:mb-0">
      <?php echo $copy; ?>
    </div>
  <?php endif; ?>

  <?php if ($form_id): ?>
    <div class="col-[main] md:col-[col-7/col-12]">
      <div class="w-full border-2 border-white rounded-lg p-6">
        <?php echo do_shortcode('[gravityform id="' . $form_id . '" title="false" ajax="true"]'); ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php $section->end(); ?>

