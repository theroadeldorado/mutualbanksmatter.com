<?php
/**
 * Copy with Media Section Template
 *
 * Two-column layout with copy on the left and media (image or Vimeo video) on the right.
 * The video will autoplay when using Vimeo.
 *
 * @package Fire
 */

// Retrieve ACF fields for this section
$tag = get_sub_field('tag');
$title = get_sub_field('title');
$copy = get_sub_field('copy');
$media_type = get_sub_field('media_type');
$image_id = get_sub_field('image');
$vimeo_video_id = get_sub_field('vimeo_video_id');
$media_content = get_sub_field('media_content');
$media_position = get_sub_field('media_position') ? get_sub_field('media_position') : 'right';

// Add custom classes to the section wrapper
$section->add_classes([
  'copy-with-media py-16 lg:py-24'
]);
?>

<?php $section->start(); ?>
<div class="fire-container grid-flow-row-dense">
  <!-- Left Column: Main Copy -->
  <?php if ($title): ?>
    <div class="mb-10 lg:mb-14">
      <?php new Fire_Heading($tag ? $tag : 'h2', $title, 'heading-2 text-white'); ?>
    </div>
  <?php endif; ?>
  <div class="<?php echo $media_position === 'right' ? 'col-[main] md:col-[col-1/col-6]' : 'col-[main] mb-6 lg:mb-0 md:col-[col-7/col-12]'; ?> space-y-6 mb-6 lg:mb-0">
    <?php if ($copy): ?>
      <div class="wizzy text-base text-white">
        <?php echo $copy; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Right Column: Media -->
  <div class="<?php echo $media_position === 'right' ? 'col-[main] md:col-[col-7/col-12]' : 'col-[main] md:col-[col-1/col-6]'; ?> space-y-6">
    <!-- Media Container -->
    <?php if ($media_type === 'video' && $vimeo_video_id): ?>
      <!-- Vimeo Video Embed -->
      <div class="relative w-full rounded-lg overflow-hidden shadow-lg aspect-video bg-black">
        <iframe
          src="https://player.vimeo.com/video/<?php echo esc_attr($vimeo_video_id); ?>?autoplay=1&loop=1&autopause=0&muted=1&background=1"
          class="absolute inset-0 w-full h-full"
          frameborder="0"
          allow="autoplay; fullscreen; picture-in-picture"
          allowfullscreen
          title="Vimeo Video"
        ></iframe>
      </div>
    <?php elseif ($media_type === 'image' && $image_id): ?>
      <!-- Image -->
      <div class="rounded-lg overflow-hidden shadow-lg">
        <?php echo ResponsivePics::get_picture($image_id, 'sm:600 338|f, md:450 338|f, lg:600 338|f, xl:656 369|f', 'lazyload-effect', true, true); ?>
      </div>
    <?php endif; ?>

    <!-- Optional Media Content Below -->
    <?php if ($media_content):
      $media_heading = $media_content['heading'];
      $media_copy = $media_content['copy'];
    ?>
      <?php if ($media_heading || $media_copy): ?>
        <div class="space-y-4">
          <?php if ($media_heading): ?>
            <div class="text-base text-white">
              <?php echo wp_kses_post($media_heading); ?>
            </div>
          <?php endif; ?>

          <?php if ($media_copy): ?>
            <div class="wizzy text-base text-white">
              <?php echo $media_copy; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php $section->end(); ?>

