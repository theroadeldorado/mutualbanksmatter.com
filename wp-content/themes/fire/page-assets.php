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
$help_email = get_field('help_email', 'site_settings');
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

  <main class="py-36 lg:py-40 site-main" x-data="{ lightbox: { open: false, type: '', src: '', alt: '' } }">
    <div class="fire-container">
      <?php get_template_part('templates/components/portal-nav/portal-nav'); ?>
      <?php if ($is_active_customer || $is_admin): ?>

        <h1 class="heading-2 mb-6 shrink-0">Assets</h1>

        <div class="hidden md:block md:col-[col-1/col-3] lg:col-[col-1/col-2]">
          <div class="flex flex-col gap-3 sticky top-10">
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

                  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-6 lg:gap-8">
                    <?php while ($assets_query->have_posts()): $assets_query->the_post();
                      $preview = get_field('preview');
                      $download_file = get_field('download_file');
                      $description = get_field('description');
                      $gradient = $gradients[array_rand($gradients)];

                      // Determine file type and preview capability
                      $file_extension = '';
                      $file_mime_type = '';
                      $can_preview = false;
                      $preview_type = '';
                      $preview_url = '';

                      if ($download_file) {
                        $file_extension = strtolower(pathinfo($download_file['filename'], PATHINFO_EXTENSION));
                        $file_mime_type = $download_file['mime_type'];

                        // Image formats
                        $image_formats = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg');
                        // Video formats
                        $video_formats = array('mp4', 'webm', 'ogg', 'mov');
                        // Audio formats
                        $audio_formats = array('mp3', 'wav', 'ogg', 'aac', 'm4a');

                        if (in_array($file_extension, $image_formats)) {
                          $can_preview = true;
                          $preview_type = 'image';
                          $preview_url = $download_file['url'];
                        } elseif (in_array($file_extension, $video_formats)) {
                          $can_preview = true;
                          $preview_type = 'video';
                          $preview_url = $download_file['url'];
                        } elseif (in_array($file_extension, $audio_formats)) {
                          $can_preview = true;
                          $preview_type = 'audio';
                          $preview_url = $download_file['url'];
                        } elseif ($file_extension === 'pdf') {
                          $can_preview = true;
                          $preview_type = 'pdf';
                          $preview_url = $download_file['url'];
                        }
                      }

                      // If there's a custom preview image, use that
                      if ($preview) {
                        $can_preview = true;
                        $preview_type = 'image';
                        $preview_url = $preview['url'];
                      }
                      ?>
                      <div class="flex flex-col gap-2">
                        <div class="rounded-xl aspect-[10/9] flex items-center justify-center overflow-hidden border-2 group border-white p-2 relative">
                          <?php if ($preview): ?>
                             <?php echo ResponsivePics::get_picture($preview['id'], 'sm:600 500|f', 'lazyload-effect full-image rounded-lg overflow-hidden', true, false); ?>
                          <?php elseif ($can_preview && $preview_type === 'image' && !$preview): ?>
                            <div class="w-full h-full flex items-center justify-center p-6 rounded-lg overflow-hidden">
                              <img src="<?php echo esc_url($preview_url); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-contain">
                            </div>
                          <?php elseif ($file_extension === 'pdf'): ?>
                            <div class="w-full h-full flex items-center justify-center p-6 rounded-lg <?php echo $gradient; ?>">
                              <span class="size-[60%] flex items-center justify-center text-white">
                                <svg class="w-full h-auto" fill="currentColor" viewBox="0 0 640 640">
                                  <path d="M192 96L320 96L320 192C320 227.3 348.7 256 384 256L480 256L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 128C160 110.3 174.3 96 192 96zM352 109.3L466.7 224L384 224C366.3 224 352 209.7 352 192L352 109.3zM192 64C156.7 64 128 92.7 128 128L128 512C128 547.3 156.7 576 192 576L448 576C483.3 576 512 547.3 512 512L512 250.5C512 233.5 505.3 217.2 493.3 205.2L370.7 82.7C358.7 70.7 342.5 64 325.5 64L192 64zM240 320C231.2 320 224 327.2 224 336C224 344.8 231.2 352 240 352L400 352C408.8 352 416 344.8 416 336C416 327.2 408.8 320 400 320L240 320zM240 416C231.2 416 224 423.2 224 432C224 440.8 231.2 448 240 448L400 448C408.8 448 416 440.8 416 432C416 423.2 408.8 416 400 416L240 416z"/>
                                </svg>
                              </span>
                            </div>
                          <?php elseif (in_array($file_extension, array('mp4', 'webm', 'ogg', 'mov'))): ?>
                            <div class="w-full h-full flex items-center justify-center p-6 rounded-lg <?php echo $gradient; ?>">
                              <span class="size-[60%] flex items-center justify-center text-white">
                                <svg class="w-full h-auto" fill="currentColor" viewBox="0 0 640 640">
                                  <path d="M512 160L398.6 160L302.6 256L387.1 256C368.7 264 351.8 274.9 337 288L96 288L96 448C96 465.7 110.3 480 128 480L278 480C280.9 491.1 284.7 501.8 289.4 512L128 512C92.7 512 64 483.3 64 448L64 192C64 156.7 92.7 128 128 128L512 128C547.3 128 576 156.7 576 192L576 276C565.1 268.1 553.3 261.4 540.9 256L544 256L544 192C544 186.9 542.8 182.2 540.7 177.9L478.1 240.5C473.4 240.2 468.7 240 464 240C452.6 240 441.3 241 430.5 242.9L513.3 160C512.9 160 512.4 160 512 160zM97.4 256L193.4 160L128 160C110.3 160 96 174.3 96 192L96 256L97.4 256zM142.7 256L257.4 256L353.4 160L238.7 160L142.7 256zM464 544C525.9 544 576 493.9 576 432C576 370.1 525.9 320 464 320C402.1 320 352 370.1 352 432C352 493.9 402.1 544 464 544zM464 288C543.5 288 608 352.5 608 432C608 511.5 543.5 576 464 576C384.5 576 320 511.5 320 432C320 352.5 384.5 288 464 288zM424.1 362.1C429.1 359.3 435.3 359.3 440.2 362.3L533.5 418.3C538.3 421.2 541.3 426.4 541.3 432C541.3 437.6 538.3 442.8 533.5 445.7L440.2 501.7C435.3 504.7 429.1 504.7 424.1 501.9C419.1 499.1 416 493.8 416 488L416 376C416 370.2 419.1 364.9 424.1 362.1zM448 459.7L494.2 432L448 404.3L448 459.8z"/>
                                </svg>
                              </span>
                            </div>
                          <?php elseif (in_array($file_extension, array('mp3', 'wav', 'ogg', 'aac', 'm4a'))): ?>
                            <div class="w-full h-full flex items-center justify-center p-6 rounded-lg <?php echo $gradient; ?>">
                              <span class="size-[60%] flex items-center justify-center text-white">
                                <svg class="w-full h-auto" fill="currentColor" viewBox="0 0 640 640">
                                  <path d="M176 384L112 384C103.2 384 96 376.8 96 368L96 272C96 263.2 103.2 256 112 256L176 256C184.5 256 192.6 252.6 198.6 246.6L316.7 128.6C317.1 128.2 317.6 128 318.1 128C319.2 128 320 128.9 320 129.9L320 510C320 511.1 319.1 511.9 318.1 511.9C317.6 511.9 317.1 511.7 316.7 511.3L198.6 393.4C192.6 387.4 184.5 384 176 384zM176 224L112 224C85.5 224 64 245.5 64 272L64 368C64 394.5 85.5 416 112 416L176 416L294.1 534.1C300.5 540.5 309.1 544 318.1 544C336.8 544 352 528.8 352 510.1L352 130C352 111.3 336.8 96.1 318.1 96.1C309.1 96.1 300.5 99.7 294.1 106L176 224zM419.2 246.4C413.9 253.5 415.3 263.5 422.4 268.8C438 280.5 448 299.1 448 320C448 340.9 438 359.5 422.4 371.2C415.3 376.5 413.9 386.5 419.2 393.6C424.5 400.7 434.5 402.1 441.6 396.8C464.9 379.3 480 351.4 480 320C480 288.6 464.9 260.7 441.6 243.2C434.5 237.9 424.5 239.3 419.2 246.4zM506.2 171.9C499.4 166.3 489.3 167.2 483.7 174C478.1 180.8 479 190.9 485.8 196.5C521.4 225.9 544 270.3 544 320C544 369.7 521.4 414.1 485.8 443.4C479 449 478 459.1 483.7 465.9C489.4 472.7 499.4 473.7 506.2 468.1C548.8 432.9 576 379.6 576 320C576 260.4 548.8 207.1 506.2 171.9z"/>
                                </svg>
                              </span>
                            </div>
                          <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center p-6 rounded-lg <?php echo $gradient; ?>">
                              <span class="size-[60%] flex items-center justify-center ">
                                <img src="<?php echo get_template_directory_uri(); ?>/theme/assets/media/images/logo-mark.png" alt="Preview" class="w-full h-auto">
                              </span>
                            </div>
                          <?php endif; ?>

                          <?php if ($download_file || $can_preview): ?>
                            <div class="absolute inset-0 flex items-center justify-center gap-6 bg-black/70 duration-300 ease-in-out transition-all opacity-0 group-hover:opacity-100">
                              <?php if ($download_file): ?>
                                <a href="<?php echo esc_url($download_file['url']); ?>" download class="flex flex-col gap-2 items-center no-underline text-white hover:text-light-blue transition-colors" title="Download <?php the_title(); ?>">
                                  <span class="size-12 flex items-center justify-center">
                                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 640 640">
                                      <path d="M160 240C160 178.1 210.1 128 272 128C311.3 128 345.8 148.2 365.8 178.8C370.2 185.5 378.9 187.9 386.1 184.5C397.5 179 410.4 176 424 176C472.6 176 512 215.4 512 264C512 278.1 508.7 291.4 502.8 303.2C500.8 307.3 500.6 312.1 502.3 316.3C504 320.5 507.5 323.9 511.8 325.4C549.2 338.5 576 374.2 576 416C576 469 533 512 480 512L176 512C114.1 512 64 461.9 64 400C64 346.3 101.8 301.4 152.2 290.5C156.5 289.6 160.3 286.9 162.5 283.1C164.7 279.3 165.4 274.8 164.2 270.5C161.5 260.8 160 250.6 160 240zM272 96C192.5 96 128 160.5 128 240C128 248 128.7 255.9 129.9 263.5C73 282.7 32 336.5 32 400C32 479.5 96.5 544 176 544L480 544C550.7 544 608 486.7 608 416C608 366.1 579.5 323 537.9 301.8C541.8 289.9 544 277.2 544 264C544 197.7 490.3 144 424 144C410.3 144 397.1 146.3 384.8 150.5C358.4 117.3 317.7 96 272 96zM403.3 371.3C409.5 365.1 409.5 354.9 403.3 348.7C397.1 342.5 386.9 342.5 380.7 348.7L336 393.4L336 272C336 263.2 328.8 256 320 256C311.2 256 304 263.2 304 272L304 393.4L259.3 348.7C253.1 342.5 242.9 342.5 236.7 348.7C230.5 354.9 230.5 365.1 236.7 371.3L308.7 443.3C314.9 449.5 325.1 449.5 331.3 443.3L403.3 371.3z"/>
                                    </svg>
                                  </span>
                                  <span class="text-sm font-bold no-underline">Download</span>
                                </a>
                              <?php endif; ?>

                              <?php if ($can_preview): ?>
                                <?php if ($preview_type === 'pdf'): ?>
                                  <a href="<?php echo esc_url($preview_url); ?>" target="_blank" rel="noopener noreferrer" class="flex gap-2 flex-col items-center text-white hover:text-light-blue no-underline transition-colors" title="Preview <?php the_title(); ?>">
                                    <span class="size-12 flex items-center justify-center">
                                      <svg class="w-full h-full" fill="currentColor" viewBox="0 0 640 640">
                                        <path d="M320 128C179.2 128 90.7 256 64 320C90.7 384 179.2 512 320 512C460.8 512 549.3 384 576 320C549.3 256 460.8 128 320 128zM127.4 176.6C174.5 132.8 239.2 96 320 96C400.8 96 465.5 132.8 512.6 176.6C559.4 220.1 590.7 272 605.6 307.7C608.9 315.6 608.9 324.4 605.6 332.3C590.7 368 559.4 420 512.6 463.4C465.5 507.1 400.8 544 320 544C239.2 544 174.5 507.2 127.4 463.4C80.6 419.9 49.3 368 34.4 332.3C31.1 324.4 31.1 315.6 34.4 307.7C49.3 272 80.6 220 127.4 176.6zM320 416C373 416 416 373 416 320C416 276.7 387.3 240.1 347.9 228.1C350.6 236.9 352 246.3 352 256C352 309 309 352 256 352C246.3 352 236.9 350.6 228.1 347.9C240 387.3 276.7 416 320 416zM192.2 327.8C192 325.2 192 322.6 192 320C192 307.8 193.7 296.1 196.9 285C197.2 284.1 197.4 283.2 197.7 282.3C210.1 241.9 242 210.1 282.4 197.6C294.3 193.9 307 192 320.1 192C322.6 192 325.1 192.1 327.5 192.2L327.9 192.2C395 196.2 448.1 251.9 448.1 320C448.1 390.7 390.8 448 320.1 448C252 448 196.3 394.8 192.3 327.8zM224.3 311.7C233.6 317 244.4 320.1 255.9 320.1C291.2 320.1 319.9 291.4 319.9 256.1C319.9 244.6 316.9 233.8 311.5 224.5C265.1 228.5 228.2 265.4 224.2 311.8z"/>
                                      </svg>
                                    </span>
                                    <span class="text-sm font-bold no-underline">Preview</span>
                                  </a>
                                <?php else: ?>
                                  <button @click="lightbox = { open: true, type: '<?php echo esc_js($preview_type); ?>', src: '<?php echo esc_url($preview_url); ?>', alt: '<?php echo esc_attr($preview['alt'] ?? get_the_title()); ?>' }" class="flex gap-2 flex-col items-center text-white hover:text-light-blue transition-colors" title="Preview <?php the_title(); ?>">
                                    <span class="size-12 flex items-center justify-center">
                                      <svg class="w-full h-full" fill="currentColor" viewBox="0 0 640 640">
                                        <path d="M320 128C179.2 128 90.7 256 64 320C90.7 384 179.2 512 320 512C460.8 512 549.3 384 576 320C549.3 256 460.8 128 320 128zM127.4 176.6C174.5 132.8 239.2 96 320 96C400.8 96 465.5 132.8 512.6 176.6C559.4 220.1 590.7 272 605.6 307.7C608.9 315.6 608.9 324.4 605.6 332.3C590.7 368 559.4 420 512.6 463.4C465.5 507.1 400.8 544 320 544C239.2 544 174.5 507.2 127.4 463.4C80.6 419.9 49.3 368 34.4 332.3C31.1 324.4 31.1 315.6 34.4 307.7C49.3 272 80.6 220 127.4 176.6zM320 416C373 416 416 373 416 320C416 276.7 387.3 240.1 347.9 228.1C350.6 236.9 352 246.3 352 256C352 309 309 352 256 352C246.3 352 236.9 350.6 228.1 347.9C240 387.3 276.7 416 320 416zM192.2 327.8C192 325.2 192 322.6 192 320C192 307.8 193.7 296.1 196.9 285C197.2 284.1 197.4 283.2 197.7 282.3C210.1 241.9 242 210.1 282.4 197.6C294.3 193.9 307 192 320.1 192C322.6 192 325.1 192.1 327.5 192.2L327.9 192.2C395 196.2 448.1 251.9 448.1 320C448.1 390.7 390.8 448 320.1 448C252 448 196.3 394.8 192.3 327.8zM224.3 311.7C233.6 317 244.4 320.1 255.9 320.1C291.2 320.1 319.9 291.4 319.9 256.1C319.9 244.6 316.9 233.8 311.5 224.5C265.1 228.5 228.2 265.4 224.2 311.8z"/>
                                      </svg>
                                    </span>
                                <span class="text-sm font-bold no-underline">Preview</span>
                              </button>
                                <?php endif; ?>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="flex justify-between gap-4">
                          <?php if ($description): ?>
                            <div class="text-sm leading-5 text-white wizzy px-2"><?php echo $description; ?></div>
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
          <?php if ($help_email): ?>
            <a href="mailto:<?php echo $help_email; ?>?subject=Help"
              class="button">
              Help
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Lightbox Modal -->
    <div
      x-trap.noscroll.noautofocus="lightbox.open"
      x-cloak
      @keydown.escape.window="lightbox.open = false"
      class="fixed inset-0 z-[1002] flex items-center justify-center p-4 bg-black/90 duration-300 ease-in-out transition-all"
      :class="{ 'opacity-0 pointer-events-none': !lightbox.open }"
      @click.self="lightbox.open = false"
    >
      <button @click="lightbox.open = false" class="absolute right-4 text-white hover:text-light-blue transition-colors z-10 <?php echo is_admin_bar_showing() ? 'top-[calc(var(--wp-admin--admin-bar--height)+1rem)]' : 'top-4'; ?>">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <div class="max-w-7xl max-h-[80vh] w-full h-full flex items-center justify-center">
        <!-- Image -->
        <img
          x-show="lightbox.type === 'image'"
          :src="lightbox.src"
          :alt="lightbox.alt"
          class="max-w-full max-h-full object-contain">

        <!-- Video -->
        <video
          x-show="lightbox.type === 'video'"
          :src="lightbox.src"
          controls
          class="max-w-full max-h-full"
          @click.stop>
          Your browser does not support the video tag.
        </video>

        <!-- Audio -->
        <div x-show="lightbox.type === 'audio'" class="w-full max-w-2xl">
          <div class="bg-white/10 rounded-lg p-8 backdrop-blur-sm">
            <div class="flex items-center justify-center mb-6">
              <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 640 640">
                <path d="M176 384L112 384C103.2 384 96 376.8 96 368L96 272C96 263.2 103.2 256 112 256L176 256C184.5 256 192.6 252.6 198.6 246.6L316.7 128.6C317.1 128.2 317.6 128 318.1 128C319.2 128 320 128.9 320 129.9L320 510C320 511.1 319.1 511.9 318.1 511.9C317.6 511.9 317.1 511.7 316.7 511.3L198.6 393.4C192.6 387.4 184.5 384 176 384zM176 224L112 224C85.5 224 64 245.5 64 272L64 368C64 394.5 85.5 416 112 416L176 416L294.1 534.1C300.5 540.5 309.1 544 318.1 544C336.8 544 352 528.8 352 510.1L352 130C352 111.3 336.8 96.1 318.1 96.1C309.1 96.1 300.5 99.7 294.1 106L176 224zM419.2 246.4C413.9 253.5 415.3 263.5 422.4 268.8C438 280.5 448 299.1 448 320C448 340.9 438 359.5 422.4 371.2C415.3 376.5 413.9 386.5 419.2 393.6C424.5 400.7 434.5 402.1 441.6 396.8C464.9 379.3 480 351.4 480 320C480 288.6 464.9 260.7 441.6 243.2C434.5 237.9 424.5 239.3 419.2 246.4zM506.2 171.9C499.4 166.3 489.3 167.2 483.7 174C478.1 180.8 479 190.9 485.8 196.5C521.4 225.9 544 270.3 544 320C544 369.7 521.4 414.1 485.8 443.4C479 449 478 459.1 483.7 465.9C489.4 472.7 499.4 473.7 506.2 468.1C548.8 432.9 576 379.6 576 320C576 260.4 548.8 207.1 506.2 171.9z"/>
              </svg>
            </div>
            <h3 class="text-white text-xl font-semibold mb-4 text-center" x-text="lightbox.alt"></h3>
            <audio
              :src="lightbox.src"
              controls
              class="w-full"
              @click.stop>
              Your browser does not support the audio tag.
            </audio>
          </div>
        </div>
      </div>
    </div>
  </main>

<?php get_footer(); ?>

