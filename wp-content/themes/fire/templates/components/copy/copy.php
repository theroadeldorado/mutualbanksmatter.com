<?php
$copy = get_sub_field('copy');
$tag = get_sub_field('tag');
$title = get_sub_field('title');

$section->add_classes([
  ''
]);
?>

<?php $section->start(); ?>
<div class="fire-container">
  <?php if ($title && $tag) : ?>
    <div class="col-[main] md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-4/col-9]">
      <?php new Fire_Heading($tag, $title, 'mb-4 heading-3 text-balance'); ?>
    </div>
  <?php endif; ?>

  <?php if ($copy): ?>
    <div class="wizzy col-[main] md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-4/col-9]">
      <?php echo $copy; ?>
    </div>
  <?php endif; ?>
</div>
<?php $section->end(); ?>