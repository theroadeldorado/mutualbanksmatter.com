<?php
$menu_name = 'primary';
$locations = get_nav_menu_locations();

// Check if menu location exists
if (!isset($locations[$menu_name])) {
  return;
}

$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

// Check if menu exists
if (!$menu) {
  return;
}

$menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );

// Build menu tree
$menu_tree = array();
$submenu_items = array();

// First pass: identify parent items and children
foreach ($menuitems as $item) {
  if ($item->menu_item_parent == 0) {
    $menu_tree[$item->ID] = $item;
    $menu_tree[$item->ID]->children = array();
  } else {
    $submenu_items[$item->ID] = $item;
  }
}

// Second pass: add children to parents
foreach ($submenu_items as $child) {
  if (isset($menu_tree[$child->menu_item_parent])) {
    $menu_tree[$child->menu_item_parent]->children[] = $child;
  }
}

$count = 0;
?>
<nav class="w-full min-h-[100svh] max-h-screen pt-32 pb-24 overflow-y-auto px-11 no-scrollbar">
  <ul class="list-none flex flex-col gap-6">
    <?php foreach( $menu_tree as $item ):
      $title = $item->title;
      $link = $item->url ? $item->url : null;
      $active = $link == get_permalink();
      $has_children = !empty($item->children);
      $show_as_button = get_field('show_as_button', $item->ID);
      $count++;
    ?>
      <?php if($link):?>
        <li class="transition-all ease-in-out <?php echo $has_children ? 'has-kids' : 'no-kids'; ?>"
            x-data="{ open: false }"
            :class="{'-translate-x-12 opacity-0 duration-200': !navOpen, 'delay-<?php echo $count;?>00 duration-500': navOpen }">

          <?php if($has_children): ?>
            <div class="flex justify-between items-center gap-4">
              <a
                href="<?php echo $link; ?>"
                target="<?php echo $item->target;?>"
                class="<?php echo $show_as_button ? 'flex items-center gap-2' : 'block'; ?> text-white text-lg no-underline"
              >
                <?php echo $title;?>
                <?php if ($show_as_button): ?>
                  <span class="size-6">
                    <?php new Fire_SVG('icon--chevron-right'); ?>
                  </span>
                <?php endif; ?>
              </a>
              <button
                @click="open = !open"
                class="w-8 h-8 flex items-center justify-center shrink-0"
                :class="{ 'rotate-180': open }"
              >
                <span class="sr-only">Toggle submenu</span>
                <span class="size-6 transition-transform duration-200">
                  <?php new Fire_SVG('icon--chevron-down'); ?>
                </span>
              </button>
            </div>

            <ul
              x-show="open"
              x-collapse
              class="list-none flex flex-col gap-2 pl-4"
            >
              <?php foreach($item->children as $child):
                $child_active = $child->url == get_permalink();
              ?>
                <li class="first:pt-4">
                  <a
                    href="<?php echo $child->url; ?>"
                    target="<?php echo $child->target;?>"
                    class="block text-white text-base no-underline"
                  >
                    <?php echo $child->title;?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <a
              href="<?php echo $link; ?>"
              target="<?php echo $item->target;?>"
              class="<?php echo $show_as_button ? 'button px-4 -ml-4' : 'block text-white text-lg no-underline';?>"
            >
              <?php echo $title;?>
              <?php if ($show_as_button): ?>
                <?php new Fire_SVG('icon--chevron-right'); ?>
              <?php endif; ?>
            </a>
          <?php endif; ?>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</nav>