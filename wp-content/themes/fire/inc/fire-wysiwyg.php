<?php
  /*
  * Adds custom css styles for wysiwyg editor
  */
  function plugin_mce_css( $mce_css ) {
    if ( !empty( $mce_css ) ) {
      $mce_css = get_template_directory_uri() . '/dist/styles.css';
      return $mce_css;
    }
  }
  add_filter( 'mce_css', 'plugin_mce_css' );

  /*
  * Adds container to MCE body class attr
  */
  function fire_wysiwyg_body_class( $mce ) {
    if ( !empty( $mce ) ) {
      $mce['body_class'] = 'prose';
    }

    return $mce;
  }
  add_filter( 'tiny_mce_before_init', 'fire_wysiwyg_body_class' );

  /*
  * adds Formats dropdown to basic and full wysiwyg toolbars
  */
  function fire_custom_toolbar($buttons) {
    array_unshift($buttons, 'styleselect');
    return $buttons;
  }
  add_filter('mce_buttons_2', 'fire_custom_toolbar');

  // adds table button
  function add_the_table_button( $buttons ) {
    array_push( $buttons, 'separator', 'table' );
    return $buttons;
  }
  add_filter( 'mce_buttons_2', 'add_the_table_button' );

  // includes table plugin
  function add_the_table_plugin( $plugins ) {
      $plugins['table'] = content_url() . '/themes/fire/inc/tinymceplugins/table/plugin.min.js';
      return $plugins;
  }
  add_filter( 'mce_external_plugins', 'add_the_table_plugin' );

  /*
  * Defines custom format options
  */
  function custom_format_styles($config) {
    $temp_array = array(
      array(
        'title' => 'Font Style Presets',
        'items' => array(
          array(
            'title' => 'Example Preset',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span',
            'classes' => 'test'
          ),
        ),
      ),
      array(
        'title' => 'Spacing Presets',
        'items' => array(
          array(
            'title' => 'None',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span, td',
            'classes' => '!mb-0'
          ),

          array(
            'title' => 'XS',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span, td',
            'classes' => '!mb-2'
          ),
          array(
            'title' => 'SM',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span, td',
            'classes' => '!mb-4'
          ),
          array(
            'title' => 'MD (Default)',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span, td',
            'classes' => '!mb-[1.875rem]'
          ),
          array(
            'title' => 'LG',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span, td',
            'classes' => '!mb-16'
          ),
          array(
            'title' => 'XL',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span, td',
            'classes' => '!mb-24'
          ),
        ),
      ),
      array(
        'title' => 'Font Family',
        'items' => array(
          array(
            'title' => 'Open Sans',
            'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span',
            'classes' => 'font-body'
          ),
        ),
      ),
      array(
        'title' => 'Balance Text',
        'selector' => 'p, a, h1, h2, h3, h4, h5, h6, li, span',
        'classes' => 'text-balance'
      ),
    );
    $config['style_formats'] = json_encode( $temp_array );
    return $config;
  }
  add_filter('tiny_mce_before_init', 'custom_format_styles');

  function wpse33318_tiny_mce_before_init( $mce_init ) {
    $mce_init['cache_suffix'] = 'v='.time();

    return $mce_init;
  }
  add_filter( 'tiny_mce_before_init', 'wpse33318_tiny_mce_before_init' );

  function openFullOptionsTinyMCE( $options ) {
    $options['wordpress_adv_hidden'] = FALSE;
    return $options;
  }
  add_filter( 'tiny_mce_before_init', 'openFullOptionsTinyMCE' );

?>
