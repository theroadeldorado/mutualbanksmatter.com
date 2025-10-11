<?php
/**
 * Home Hero Template for Fire Theme
 *
 * Displays a full-width hero section with optional logo, heading, button, and background media (image or Vimeo video).
 */

// Retrieve ACF fields
$show_logo = get_sub_field('show_logo');
$media_type = get_sub_field('media_type');
$tag = get_sub_field('tag');
$title = get_sub_field('title');
$button = get_sub_field('button');
$image_id = get_sub_field('image');
$vimeo_video_id = get_sub_field('vimeo_video_id');

// Get theme logo path
$logo_path = get_template_directory_uri() . '/theme/assets/media/images/logo.png';

// Add section classes for identification
$section->add_classes([
  'home-hero relative max-w-[100vw] overflow-x-clip'
]);
?>

<?php $section->start(); ?>
<div class="grid-stack">
  <div class="w-screen h-screen relative">
    <?php if ($media_type === 'video' && $vimeo_video_id): ?>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 min-w-screen min-h-screen aspect-[16/9]">
        <div class="relative w-full h-full">
          <iframe
            src="https://player.vimeo.com/video/<?php echo esc_attr($vimeo_video_id); ?>?autoplay=1&loop=1&muted=1&background=1&controls=0"
            class="full-image absolute inset-0 w-full h-full object-cover"
            frameborder="0"
            allow="autoplay; fullscreen"
            allowfullscreen>
          </iframe>
        </div>
      </div>
    <?php elseif ($media_type === 'image' && $image_id): ?>
      <?php echo ResponsivePics::get_picture($image_id, 'sm:600 800|f, md:900 1200|f, lg:1200 1600|f, xl:1920 1920|f', 'lazyload-effect full-image absolute inset-0 object-cover', true, true); ?>
    <?php endif; ?>
  </div>

  <div class="relative z-[1] bg-gradient-to-tr from-light-blue/90 via-light-blue/50 to-light-blue/0" aria-hidden="true">
  </div>



  <div class="relative z-[1] flex items-center min-h-screen py-16 lg:py-24">
    <div class="fire-container">
      <div class="text-left col-[main]">
        <div class="space-y-6">
          <?php if ($show_logo): ?>
            <div class="mb-8">
              <img
                src="<?php echo esc_url($logo_path); ?>"
                alt="Mutual Banks Matter"
                class="h-32 lg:h-40 xl:h-52 w-auto"
              />
            </div>
          <?php endif; ?>

          <?php if ($title && $tag): ?>
            <?php new Fire_Heading($tag ? $tag : 'h1', $title, 'heading-2 lg:heading-1 text-charcoal text-balance '); ?>
          <?php endif; ?>

          <?php if ($button): ?>
            <div class="mt-8">
              <a
                href="<?php echo esc_url($button['url']); ?>"
                class="button-charcoal"
                <?php if ($button['target']): ?>target="<?php echo esc_attr($button['target']); ?>"<?php endif; ?>
              >
                <?php echo esc_html($button['title']); ?>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $section->end(); ?>

