<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Fire
 */
if (!function_exists('get_field')) {
  wp_die('This theme requires the Advanced Custom Fields plugin to be installed and active. <a href="/wp-admin/plugins.php">Plugins Page</a>');
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php
    $bugherd_api_key = get_field('bugherd_api_key', 'site_settings');
    $bugherd_enabled = get_field('bugherd_enabled', 'site_settings');
    if ($bugherd_api_key && $bugherd_enabled) :
  ?>
    <script type="text/javascript" src="https://www.bugherd.com/sidebarv2.js?apikey=<?php print $bugherd_api_key; ?>" async="true"></script>
  <?php endif; ?>

  <?php

    $global_scripts = function_exists('get_field') ? get_field('scripts', 'site_settings') : false;


    fire_print_scripts_at_location($global_scripts, 'head-before');

    wp_head();

    fire_print_scripts_at_location($global_scripts, 'head-after');

   ?>
</head>

<body <?php body_class(); ?>>

<?php
  fire_print_scripts_at_location($global_scripts, 'body-before');
?>

<?php wp_body_open(); ?>
<div id="page" class="site">
  <a class="sr-only skip-link focus:not-sr-only" href="#primary"><?php esc_html_e( 'Skip to content', 'fire' ); ?></a>

  <header class="site-header fire-container my-4">

    <a class="block w-8" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
      <?php new Fire_SVG('logo'); ?>
      <span class="sr-only"><?php bloginfo( 'name' ); ?></span>
    </a>

    <nav id="site-navigation" class="main-navigation">
      <?php
        wp_nav_menu([
          'container' => false,
          'depth' => 2,
          'theme_location' => 'primary',
          'menu_class' => 'menu_class',
          'item_0' => 'item_class',
          'link_0' => 'list_item_class',
          'submenu_0' => 'submenu',
          // 'alpine_link_0' => '$store.navOpen ?? `opacity-0 -translate-y-1/2`',
        ]);
      ?>
    </nav>

    <?php
    $fire_description = get_bloginfo( 'description', 'display' );
    if ( $fire_description || is_customize_preview() ) :
      ?>
      <p class="site-description"><?php echo $fire_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
    <?php endif; ?>
  </header>

