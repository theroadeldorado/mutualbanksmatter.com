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
    <div>
      <?php new Fire_Heading($tag, $title, 'mb-4 text-balance'); ?>
    </div>
  <?php endif; ?>

  <?php if ($copy): ?>
    <div class="wizzy">
      <?php echo $copy; ?>
    </div>
  <?php endif; ?>
</div>
<?php $section->end(); ?>