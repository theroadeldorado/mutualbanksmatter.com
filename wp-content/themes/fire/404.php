<?php
  /**
   * The template for displaying 404 pages (not found)
   *
   * @link https://codex.wordpress.org/Creating_an_Error_404_Page
   */
?>


<?php get_header(); ?>
  <main id="primary" class="site-main flex items-center justify-center">
    <div class="fire-container py-16 lg:py-24">
      <div class="text-center">
      <h1 class="page-title heading-4 text-white"><?php _e( 'Oops! That page can&rsquo;t be found.', 'fire' ); ?></h1>
      <a class="mt-8 button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php _e('Back Home', 'fire'); ?></a>
      </div>
    </div>
  </main>

<?php get_footer(); ?>