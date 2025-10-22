<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Fire
 */

$global_scripts = function_exists('get_field') ? get_field('scripts', 'site_settings') : false;
$mission = function_exists('get_field') ? get_field('mission', 'site_settings') : false;
$contact_info = function_exists('get_field') ? get_field('contact_info', 'site_settings') : false;
?>

  <footer class="text-white pb-20 pt-12 lg:py-20  <?php echo is_front_page() ? 'bg-blue' : 'bg-charcoal'; ?>">
    <div class="fire-container relative z-[1]">
      <div class="col-[main] md:col-[col-1]">
        <a href="<?php echo home_url(); ?>">
          <img src="<?php echo get_template_directory_uri(); ?>/theme/assets/media/images/logo-mark.png" alt="<?php echo get_bloginfo('name'); ?>" class="w-16 h-auto">
        </a>
      </div>
      <div class="col-[main] md:col-[col-2/col-7] text-base mt-8 md:mt-0">
        <?php if ($mission): ?>
          <div class="wizzy mb-6">
            <?php echo $mission; ?>
          </div>
          <p><?php echo sprintf('© %s %s', date('Y'), get_bloginfo('name')); ?></p>
        <?php endif; ?>
      </div>
      <div class="col-[main] md:col-[col-8/col-9] mt-8 md:mt-0">
        <?php
          wp_nav_menu(
            array(
              'container'       => false,
              'depth'           => 1,
              'menu_class'      => 'flex flex-col gap-1',
              'link_0'      => 'text-white underline underline-offset-1 decoration-1 hover:no-underline focus:no-underline',
            )
          );
        ?>
      </div>
      <div class="col-[main] md:col-[col-10/col-12] mt-8 md:mt-0">
        <div class="flex md:justify-end">
          <?php if($contact_info): ?>
            <div class="text-base wizzy">
              <?php echo $contact_info; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </footer>
</div><!-- #page -->

<a href="#top" class="fixed bottom-4 right-4 z-[10000] rounded-full bg-light-blue flex items-center justify-center size-16 rotate-180 p-2 text-charcoal">
  <span class="sr-only">
    <?php esc_html_e('Back to top', 'fire'); ?>
  </span>
  <?php new Fire_SVG('icon--chevron-down'); ?>
</a>

<?php if (is_front_page()): ?>
  <div class="pointer-events-none fixed right-0 bottom-0 text-navy">
    <?php new Fire_SVG('icon--bg-logo'); ?>
  </div>
<?php endif; ?>

<?php
  // Check if environment is local
  if (!function_exists('is_wpe')) {
    require get_template_directory() . '/templates/components/grid-debug/grid-debug.php';
  }

  wp_footer();

  fire_print_scripts_at_location($global_scripts, 'body-after');

  ?>

</body>
</html>
