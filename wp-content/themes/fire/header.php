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

 $sections = get_field('sections');
  $hide_logo = false;

  if($sections && $sections[0]['acf_fc_layout'] === 'home_hero') {
    $hide_logo = true;
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

  <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/theme/assets/media/favicons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="<?php echo get_template_directory_uri(); ?>/theme/assets/media/favicons/favicon.svg" />
  <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/theme/assets/media/favicons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/theme/assets/media/favicons/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="MyWebSite" />
  <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/theme/assets/media/favicons/site.webmanifest" />

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
<div id="page" class="site relative z-[1]">
  <a class="sr-only skip-link focus:not-sr-only" href="#primary"><?php esc_html_e( 'Skip to content', 'fire' ); ?></a>

  <header x-data="{ navOpen: false }" class="site-header fire-container absolute top-0 left-0 right-0 z-[1001] py-8">
    <div class="flex items-center <?php echo $hide_logo ? 'justify-end' : 'justify-between'; ?>">
      <?php if(!$hide_logo): ?>
        <a href="<?php echo home_url(); ?>">
          <img src="<?php echo get_template_directory_uri(); ?>/theme/assets/media/images/logo-mark.png" alt="<?php echo get_bloginfo('name'); ?>" class="w-16 h-auto">
        </a>
      <?php endif; ?>
      <button class="ml-2 w-7 h-[22px] lg:hidden group relative z-[1002]" @click="navOpen = ! navOpen">
        <span class="sr-only"><?php _e('Toggle navigation', 'fire'); ?></span>
        <span class="w-full duration-150 transition h-[4px] rounded-full bg-white absolute top-1/2 left-1/2 -translate-x-1/2 ease-in-out translate-y-[-11px]" :class="{'rotate-45 translate-y-0': navOpen, 'translate-y-[-11px]': !navOpen}"></span>
        <span class="duration-150 transition h-[4px] rounded-full bg-white absolute top-1/2 left-1/2 -translate-x-1/2 ease-in-out translate-y-[-1px] w-full" :class="{'rotate-45 w-0 translate-y-0': navOpen, 'translate-y-[-1px] w-full': !navOpen}"></span>
        <span class="w-full duration-150 transition h-[4px] rounded-full bg-white absolute top-1/2 left-1/2 -translate-x-1/2 ease-in-out translate-y-[9px]" :class="{'-rotate-45 translate-y-0': navOpen, 'translate-y-[9px] ': !navOpen}"></span>
      </button>

      <div x-cloak x-trap.noscroll.noautofocus="navOpen" class="fixed flex items-end w-screen h-screen duration-200 ease-in-out transition-opacity z-[1001]  inset-0 main-navigation lg:hidden bg-charcoal" :class="{'opacity-0 pointer-events-none': !navOpen}">
        <?php require get_template_directory() . '/templates/components/mobile-nav/mobile-nav.php';?>
      </div>


      <nav id="site-navigation" class="main-navigation hidden lg:flex items-center justify-end">
        <?php
          wp_nav_menu([
            'container' => false,
            'depth' => 2,
            'theme_location' => 'primary',
            'menu_class' => 'flex items-center gap-6',
            'item_0' => 'item_class group relative py-5',
            'link_0' => 'text-white text-lg no-underline',
            'submenu_0' => 'flex opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-100 duration-300 ease-in-out group-hover:flex absolute top-full left-1/2 -translate-x-1/2 p-4 text-base font-san-serif rounded-sm w-[200px] shadow-md bg-charcoal/80 z-[1001] flex-col gap-2',
            'link_1' => 'text-white hover:text-light-blue focus-visible:text-light-blue block w-full no-underline',
          ]);
        ?>
      </nav>
    </div>
  </header>

