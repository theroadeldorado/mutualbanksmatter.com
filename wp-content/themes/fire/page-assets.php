<?php
/**
 * Template Name: Assets Portal
 *
 * Customer portal page for viewing and downloading assets
 */

get_header();

// Redirect to login if not logged in
if (!is_user_logged_in()) {
  wp_redirect(wp_login_url(get_permalink()));
  exit;
}

$current_user = wp_get_current_user();
$is_active_customer = fire_is_active_customer();
$is_admin = current_user_can('manage_options');
$categories = get_terms(array(
  'taxonomy' => 'asset-category',
  'hide_empty' => true,
  'orderby' => 'name',
  'order' => 'ASC',
));

$gradients =['bg-linear-to-tr from-cyan-700 via-blue-400 to-indigo-600', 'bg-linear-to-bl from-pink-200 via-purple-400 to-indigo-600', 'bg-linear-to-r from-purple-500 via-indigo-500 to-blue-500', 'bg-linear-to-r from-indigo-500 via-purple-500 to-pink-500', 'bg-linear-to-r from-purple-200 via-violet-400 to-indigo-600'];
?>

  <main class="py-36 lg:py-40 site-main" x-data="{ lightbox: { open: false, image: '', alt: '' } }">
    <div class="fire-container">
      <?php get_template_part('templates/components/portal-nav/portal-nav'); ?>
      <?php if ($is_active_customer ): ?>
      <!-- || $is_admin -->


        <h1 class="text-4xl lg:text-5xl font-bold mb-8 text-white">Assets</h1>

        <div class="hidden md:block md:col-[col-1/col-3] lg:col-[col-1/col-2]">
          <div class="flex flex-col gap-3 sticky top-0">
            <?php foreach ($categories as $category): ?>
              <a href="#<?php echo esc_attr($category->slug); ?>" class="text-white no-underline hover:text-light-blue font-medium"><?php echo esc_html($category->name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="col-[main] md:col-[col-4/col-12] lg:col-[col-3/col-12] flex flex-col gap-10">
          <?php if ($categories && !is_wp_error($categories)):
            foreach ($categories as $category):
              $args = array(
                'post_type' => 'asset',
                'posts_per_page' => -1,
                'tax_query' => array(
                  array(
                    'taxonomy' => 'asset-category',
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                  ),
                ),
                'orderby' => 'title',
                'order' => 'ASC',
              );

              $assets_query = new WP_Query($args);

              if ($assets_query->have_posts()): ?>
                <div id="<?php echo esc_attr($category->slug); ?>">
                  <h2 class="text-2xl lg:text-3xl font-semibold mb-6 text-white"><?php echo esc_html($category->name); ?></h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php while ($assets_query->have_posts()): $assets_query->the_post();
                      $preview = get_field('preview');
                      $download_file = get_field('download_file');
                      $description = get_field('description');
                      $gradient = $gradients[array_rand($gradients)]; ?>
                      <div class="flex flex-col gap-4">
                        <div class="rounded-xl aspect-[10/9] flex items-center justify-center overflow-hidden border-4 group border-white p-4 relative">
                          <?php if ($preview): ?>
                            <img src="<?php echo esc_url($preview['sizes']['medium'] ?? $preview['url']); ?>" alt="<?php echo esc_attr($preview['alt']); ?>" class="w-full h-auto rounded-lg">
                          <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center p-6 rounded-lg <?php echo $gradient; ?>">
                              <span class="size-[60%] flex items-center justify-center ">
                                <img src="<?php echo get_template_directory_uri(); ?>/theme/assets/media/images/logo-mark.png" alt="Preview" class="w-full h-auto">
                              </span>
                            </div>
                          <?php endif; ?>

                          <?php if ($download_file || $preview): ?>
                            <div class="absolute inset-0 flex items-center justify-center gap-6 bg-black/70 duration-300 ease-in-out transition-all opacity-0 group-hover:opacity-100">
                              <?php if ($download_file): ?>
                                <a href="<?php echo esc_url($download_file['url']); ?>" download class="flex flex-col gap-2 items-center text-white hover:text-light-blue transition-colors" title="Download <?php the_title(); ?>">
                                  <span class="size-12 flex items-center justify-center"><?php new Fire_SVG('icon--download'); ?></span>
                                  <span class="text-sm font-bold no-underline">Download</span>
                                </a>
                              <?php endif; ?>

                              <?php if($preview): ?>
                              <button @click="lightbox = { open: true, image: '<?php echo esc_url($preview['url']); ?>', alt: '<?php echo esc_attr($preview['alt'] ?: get_the_title()); ?>' }" class="flex gap-2 flex-col items-center text-white hover:text-light-blue transition-colors " title="Preview <?php the_title(); ?>">
                                <span class="size-12 flex items-center justify-center"><?php new Fire_SVG('icon--preview'); ?></span>

                                <span class="text-sm font-bold no-underline">Preview</span>
                              </button>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="flex justify-between gap-4">
                          <?php if ($description): ?>
                            <div class="text-sm text-white wizzy"><?php echo esc_html($description); ?></div>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </div>
                </div>
              <?php endif;  wp_reset_postdata(); ?>
            <?php endforeach;?>
          <?php endif;?>
        </div>
      <?php else: ?>
        <!-- Inactive Customer: Show Help Button -->
        <div class="max-w-2xl mx-auto text-center py-20">
          <svg class="w-20 h-20 mx-auto mb-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>

          <h1 class="text-3xl lg:text-4xl font-bold mb-4 text-white">Access Restricted</h1>
          <p class="text-lg text-white mb-8">Your account is currently inactive. Please contact us to activate your access to the assets portal.</p>

          <a href="mailto:join@mutualbanksmatter.com?subject=Help"
             class="button button-light-blue inline-flex items-center gap-2 text-lg">
            Help
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Lightbox Modal -->
    <div
      x-trap.noscroll.noautofocus="lightbox.open"
      x-cloak
      @keydown.escape.window="lightbox.open = false"
      class="fixed inset-0 z-[1002] flex items-center justify-center p-4 bg-black/90"
      :class="{ 'opacity-0 pointer-events-none': !lightbox.open }"
      @click.self="lightbox.open = false"
    >
      <button @click="lightbox.open = false" class="absolute right-4 text-white hover:text-light-blue transition-colors z-10 <?php echo is_admin_bar_showing() ? 'top-[calc(var(--wp-admin--admin-bar--height)+1rem)]' : 'top-4'; ?>">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <div class="max-w-7xl max-h-full w-full h-full flex items-center justify-center">
        <img :src="lightbox.image" :alt="lightbox.alt"class="max-w-full max-h-full object-contain">
      </div>
    </div>
  </main>

<?php get_footer(); ?>

