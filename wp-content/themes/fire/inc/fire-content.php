<?php
  /**
   * Turns off the Block editor
   */
  add_filter( 'use_block_editor_for_post', '__return_false' );

  /**
   * Hides the default content field
   */
  add_action( 'init', 'fire_remove_page_editor', 999 );
  function fire_remove_page_editor() {
      remove_post_type_support( 'page', 'editor' );
  }
?>
