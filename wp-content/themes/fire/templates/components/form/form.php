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
$layout = get_sub_field('layout');
$type = get_sub_field('type');
$embed_code = get_sub_field('embed');

$section->add_classes([
  'map py-16 lg:py-24'
]);
?>

<?php $section->start(); ?>
<div class="fire-container">
  <?php if ($title): ?>
    <div class="mb-8 col-[main] <?php echo $layout === '2-col' ? '' : 'md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-4/col-9]'; ?>">
      <?php new Fire_Heading($tag ? $tag : 'h2', $title, $layout === '2-col' ? 'heading-3' : 'heading-2' . ' text-white'); ?>
    </div>
  <?php endif; ?>

  <?php if ($copy): ?>
    <div class="wizzy mb-10 col-[main]  <?php echo $layout === '2-col' ? 'lg:mb-0 md:col-[col-1/col-6]' : 'md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-4/col-9]'; ?>">
      <?php echo $copy; ?>
    </div>
  <?php endif; ?>

  <?php if ($form_id && $type === 'gf'): ?>
    <div class="col-[main] <?php echo $layout === '2-col' ? 'md:col-[col-7/col-12]' : 'md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-4/col-9]'; ?>">
      <div class="w-full border-2 border-white rounded-lg p-6">
        <?php echo do_shortcode('[gravityform id="' . $form_id . '" title="false" ajax="true"]'); ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($embed_code && $type === 'embed'): ?>
    <div class="col-[main] <?php echo $layout === '2-col' ? 'md:col-[col-7/col-12]' : 'md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-4/col-9]'; ?>">
      <?php echo $embed_code; ?>
    </div>
  <?php endif; ?>
</div>
<?php $section->end(); ?>

