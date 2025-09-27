<?php
  $social_links = get_field('social_links', 'site_settings');
  if (empty($social_links)) {
    return;
  }
  foreach ($social_links as $platform => $link) {
    if($link):?>
      <a class="block w-5 text-white no-underline" target="_blank" href="<?php echo $link;?>">
        <?php new Fire_SVG('icon--social-' . $platform); ?>
        <span class="sr-only"><?php echo $platform; ?></span>
      </a>
    <?php
    endif;
  }
?>
