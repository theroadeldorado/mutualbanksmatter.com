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

?>

  <footer class="text-white bg-gray-500 fire-container">
    <div>
      <div>
        <?php
          wp_nav_menu(
            array(
              'container'       => false,
              'depth'           => 2,
              'theme_location'  => 'footer',
              'menu_class'      => 'menu_class',
              'link_class'      => 'link_class',
              'sub_link_class' => 'sub_link_class',
              'sub_menu_class' => 'sub_menu_class',
            )
          );
        ?>
      </div>
      <?php echo sprintf('© %s %s', date('Y'), get_bloginfo('name')); ?>
      <div class="flex items-center justify-start space-x-2">
        <?php require get_template_directory() . '/templates/components/social-links/social-links.php';?>
      </div>
    </div>
  </footer>
</div><!-- #page -->

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
