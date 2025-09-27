<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Fire
 */

get_header();
?>

  <main id="primary" class="site-main">

    <?php
    while ( have_posts() ) :
      the_post();

      if(!post_password_required()) {
        get_template_part( 'templates/content', 'page' );
      } else {
        print '<div class="fire-container py-10">';
          echo get_the_password_form();
        echo '</div>';
      }

    endwhile; // End of the loop.
    ?>

  </main><!-- #main -->

<?php
get_footer();
