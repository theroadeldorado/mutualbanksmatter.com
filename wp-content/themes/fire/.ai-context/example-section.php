<?php
/**
 * Example Section Template for Fire Theme
 *
 * This file demonstrates the standard structure for a section template using ACF fields in the Fire WordPress theme.
 *
 * - Retrieves ACF sub fields (copy, tag, title)
 * - Adds custom classes to the section wrapper
 * - Uses $section->start() and $section->end() to wrap content
 * - Renders a heading using the Fire_Heading component if both tag and title are present
 * - Outputs the copy field if it exists
 *
 * Use this as a reference when creating new section templates. Follow the same structure for consistency.
 */

// Retrieve ACF sub fields for this section
$copy = get_sub_field('copy');

// Heading group clone (group_66bcb4cd848b9) use $tag and $title fields to render the heading
$tag = get_sub_field('tag');
$title = get_sub_field('title');

// Add custom classes to the section wrapper (array can be extended as needed)
$section->add_classes([
  ''
]);
?>

<?php $section->start(); // Begin section output (handles wrapper markup and classes) ?>
<div class="fire-container"><!-- Main container for section content -->
  <?php if ($title && $tag): ?> <!-- If both title and tag exist, render heading -->
    <div>
      <?php // Use Fire_Heading component to render the section heading (tag, text, and optional classes)
        new Fire_Heading($tag ? $tag : 'h2', $title, 'text-balance'); ?>
    </div>
  <?php endif; ?>

  <?php if ($copy): ?> <!-- If copy exists, render it inside a styled div -->
    <div class="wizzy">
      <?php echo $copy; ?>
    </div>
  <?php endif; ?>
</div>
<?php $section->end(); // End section output (closes wrapper) ?>