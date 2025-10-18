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
?>

  <main class="py-36 lg:py-40 site-main">
    <div class="px-8 lg:px-12">
      <?php get_template_part('templates/components/portal-nav/portal-nav'); ?>

      <?php if ($is_active_customer || $is_admin): ?>
        <!-- Active Customer: Show Assets -->
        <div>
          <h1 class="text-4xl lg:text-5xl font-bold mb-8 text-white">Assets</h1>

          <?php
          // Get all asset categories
          $categories = get_terms(array(
            'taxonomy' => 'asset-category',
            'hide_empty' => true,
          ));

          if ($categories && !is_wp_error($categories)):
            foreach ($categories as $category):
              // Query assets for this category
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

              if ($assets_query->have_posts()):
          ?>

          <div class="mb-16">
            <h2 class="text-2xl lg:text-3xl font-semibold mb-6 text-white"><?php echo esc_html($category->name); ?></h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
              <?php while ($assets_query->have_posts()): $assets_query->the_post();
                $preview = get_field('preview');
                $download_file = get_field('download_file');
              ?>

              <div class="bg-white rounded-lg overflow-hidden shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                <?php if ($preview): ?>
                  <div class="relative overflow-hidden" style="padding-bottom: 75%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <img src="<?php echo esc_url($preview['sizes']['medium'] ?? $preview['url']); ?>"
                         alt="<?php echo esc_attr($preview['alt']); ?>"
                         class="absolute inset-0 w-full h-full object-cover">

                    <!-- Overlay buttons -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-4">
                      <?php if ($download_file): ?>
                        <a href="<?php echo esc_url($download_file['url']); ?>"
                           download
                           class="flex flex-col items-center text-white hover:text-blue-300 transition-colors"
                           title="Download <?php the_title(); ?>">
                          <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                          </svg>
                          <span class="text-sm">Download</span>
                        </a>
                      <?php endif; ?>

                      <a href="<?php echo esc_url($preview['url']); ?>"
                         target="_blank"
                         class="flex flex-col items-center text-white hover:text-blue-300 transition-colors"
                         title="Preview <?php the_title(); ?>">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-sm">Preview</span>
                      </a>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="bg-white p-6">
                  <h3 class="text-lg font-semibold mb-2"><?php the_title(); ?></h3>

                  <?php if ($download_file): ?>
                    <div class="text-sm text-gray-600 mb-4">
                      <?php echo esc_html(strtoupper($download_file['subtype'])); ?> ·
                      <?php echo size_format($download_file['filesize']); ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($download_file): ?>
                    <a href="<?php echo esc_url($download_file['url']); ?>"
                       download
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                      </svg>
                      Download
                    </a>
                  <?php endif; ?>
                </div>
              </div>

              <?php endwhile; ?>
            </div>
          </div>

          <?php
              endif;
              wp_reset_postdata();
            endforeach;
          else:
            // No categories found
            echo '<p class="text-lg text-gray-400">No assets available at this time.</p>';
          endif;
          ?>
        </div>

      <?php else: ?>
        <!-- Inactive Customer: Show Message -->
        <div class="max-w-2xl mx-auto text-center py-20">
          <svg class="w-20 h-20 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>

          <h1 class="text-3xl lg:text-4xl font-bold mb-4 text-white">Access Restricted</h1>
          <p class="text-lg text-gray-400 mb-8">Your account is currently inactive. Please contact us to activate your access to the assets portal.</p>

          <a href="mailto:join@mutualbanksmatter.com?subject=Help - Activate Account"
             class="button button-light-blue inline-flex items-center gap-2 text-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Contact Help
          </a>
        </div>
      <?php endif; ?>

    </div>
  </main>

<?php get_footer(); ?>

