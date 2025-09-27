<?php


add_action( 'after_setup_theme', 'fire_columns_theme_setup' );
if ( ! function_exists( 'fire_columns_theme_setup' ) ) {
  function fire_columns_theme_setup(){
    add_action( 'admin_init', 'fire_columns_theme_add_editor_styles' );
    add_action( 'init', 'fire_columns_buttons' );
  }
}

/********* Registers an editor stylesheet for the theme ***********/
if ( ! function_exists( 'fire_columns_theme_add_editor_styles' ) ) {
  function fire_columns_theme_add_editor_styles() {
      add_editor_style( 'fire-columns.css' );
  }
}

/********* TinyMCE Buttons ***********/
if ( ! function_exists( 'fire_columns_buttons' ) ) {
  function fire_columns_buttons() {
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
          return;
      }

      if ( get_user_option( 'rich_editing' ) !== 'true' ) {
          return;
      }

      add_filter( 'mce_external_plugins', 'fire_columns_add_buttons' );
      add_filter( 'mce_buttons', 'fire_columns_register_buttons' );
  }
}

if ( ! function_exists( 'fire_columns_add_buttons' ) ) {
  function fire_columns_add_buttons( $plugin_array ) {
      $plugin_array['columns'] = get_template_directory_uri().'/inc/fire-columns/fire-columns.js';
      return $plugin_array;
  }
}

if ( ! function_exists( 'fire_columns_register_buttons' ) ) {
  function fire_columns_register_buttons( $buttons ) {
      array_push( $buttons, 'TwoColumns', 'ThreeColumns' );
      return $buttons;
  }
}