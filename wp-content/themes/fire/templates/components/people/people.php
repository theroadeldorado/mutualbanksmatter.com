<?php
/**
 * People Section Template
 *
 * Displays a grid of people with their photos, names, titles, and companies.
 * Shows 4 columns on desktop, 2 on tablet, and 1 on mobile.
 *
 * @package Fire
 */

$tag = get_sub_field('tag');
$title = get_sub_field('title');

// Add custom classes to the section wrapper
$section->add_classes([
  'people py-16 lg:py-24'
]);
?>

<?php $section->start(); ?>
  <div class="fire-container">
		<?php if ($title): ?>
			<div class="mb-10 lg:mb-14">
				<?php new Fire_Heading($tag ? $tag : 'h2', $title, 'heading-2 text-white'); ?>
			</div>
		<?php endif; ?>
    <?php if (have_rows('people')): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php while (have_rows('people')): the_row();
          $image_id = get_sub_field('image');
          $name = get_sub_field('name');
          $title = get_sub_field('title');
          $company = get_sub_field('company');
        ?>
          <div class="space-y-4 rounded-xl overflow-hidden p-5 border-2 border-white text-white">
            <?php if ($image_id): ?>
              <div class="rounded-lg overflow-hidden w-full">
                <?php echo ResponsivePics::get_picture($image_id, 'sm:600 600|f, md:375 375|f, lg:300 300|f, xl:400 400|f', 'lazyload-effect full-image', true, true); ?>
              </div>
            <?php endif; ?>

            <div class="space-y-1">
              <?php if ($name): ?>
                <h3 class="heading-4 mb-2">
                  <?php echo esc_html($name); ?>
                </h3>
              <?php endif; ?>

              <?php if ($title): ?>
                <p class="heading-6 mb-2">
                  <?php echo esc_html($title); ?>
                </p>
              <?php endif; ?>

              <?php if ($company): ?>
                <p class="heading-6">
                  <?php echo esc_html($company); ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
<?php $section->end(); ?>

