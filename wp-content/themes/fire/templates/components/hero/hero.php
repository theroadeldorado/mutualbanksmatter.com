<?php
  $image = get_sub_field('image');
  $copy = get_sub_field('copy');

  $section->add_classes([
    ''
  ]);
?>

<?php $section->start(); ?>

<?php if($image):?>
  <?php echo ResponsivePics::get_picture($image['id'], 'sm:600 100|f, md:900 150|f, lg:1200 300|f, xl:1920 500|f', 'lazyload-effect', true, true); ?>
<?php endif; ?>

<?php $section->end(); ?>
