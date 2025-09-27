<?php
  function fire_excerpt_length( $length ) {
    return 20;
  }
  add_filter( 'excerpt_length', 'fire_excerpt_length', 999 );

  function fire_excerpt_more( $more ) {
    return '&hellip;';
  }
  add_filter( 'excerpt_more', 'fire_excerpt_more' );

  function acf_excerpt($acf_field) {
    $text = get_field($acf_field);
    if ( '' !== $text ) {
      $text = strip_shortcodes( $text );
      $text = apply_filters('the_content', $text);
      $text = str_replace(']]>', ']]>', $text);
      $excerpt_length = apply_filters('excerpt_length','fire_excerpt_length');
      $excerpt_more = apply_filters('excerpt_more','fire_excerpt_more');
      $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
    }
    return apply_filters('the_excerpt', $text);
  }
