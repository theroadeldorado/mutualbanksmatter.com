<?php

  // EXAMPLE MENU
  // wp_nav_menu([
  //   'container' => false,
  //   'depth' => 2,
  //   'theme_location' => 'primary',
  //   'menu_class' => 'menu_class',
  //   'item_0' => 'item_class',
  //   'link_0' => 'list_item_class',
  //   'submenu_0' => 'hidden',
  //   'alpine_link_0' => '$store.navOpen ?? `opacity-0 -translate-y-1/2`',
  // ]);

  /**
   * Register additional menus
   */
  function fire_register_nav_menu(){
    register_nav_menus( array(
      'primary' => __( 'Primary', 'fire' ),
      'footer'  => __( 'Footer', 'fire' )
    ) );
  }
  add_action( 'after_setup_theme', 'fire_register_nav_menu', 0 );

  add_filter('nav_menu_css_class', function($classes, $menu_item, $args, $depth) {
    if (property_exists($args, 'item_'. $depth)) {
      $classes[] = $args->{'item_' . $depth};
    }
    return $classes;
  }, 10, 4);

  add_filter('nav_menu_link_attributes', function($atts, $item, $args, $depth) {
    if (property_exists($args, 'link_' . $depth)) {
      $atts['class'] = $args->{'link_' . $depth};
    }
    if (property_exists($args, 'alpine_link_' . $depth)) {
      $atts[':class'] = $args->{'alpine_link_' . $depth};
    }
    return $atts;
  }, 10, 4);


  add_filter('nav_menu_submenu_css_class', function($classes, $args, $depth) {
    if (property_exists($args, 'submenu_' . $depth)) {
      $classes[] = $args->{'submenu_' . $depth};
    }
    return $classes;
  }, 10, 3);

  // adds menu tree
  function generate_menu_tree($menu_name) {
    $tree = [];
    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object($locations[$menu_name]);
    $items = wp_get_nav_menu_items($menu->term_id, array('order' => 'DESC'));

    $active_menu_item = current(wp_filter_object_list($items, array('object_id' => get_queried_object_id())));

    foreach ($items as $item) {
      // add parents to tree
      if ($item->menu_item_parent == '0') {
        $item->children = [];
        if (isset($active_menu_item->ID) && $item->ID == $active_menu_item->ID) {
          $item->active_nav_item = true;
        } else {
          $item->active_nav_item = false;
        }
        $tree[$item->ID] = $item;
      }

      // add children to tree
      foreach ($tree as $first_level_branch) {
        if ($first_level_branch->ID == $item->menu_item_parent) {
          $item->children = [];
          if (isset($active_menu_item->ID) && $item->ID == $active_menu_item->ID) {
            $item->active_nav_item = true;
          } else {
            $item->active_nav_item = false;
          }
          $tree[$first_level_branch->ID]->children[$item->ID] = $item;
        }
      }

      // add grandchildren to tree
      foreach ($tree as $parent) {
        foreach ($parent->children as $child) {
          if ($child->ID == $item->menu_item_parent) {
            if (isset($active_menu_item->ID) && $item->ID == $active_menu_item->ID) {
              $item->active_nav_item = true;
            } else {
              $item->active_nav_item = false;
            }
            $tree[$parent->ID]->children[$child->ID]->children[] = $item;
          }
        }
      }
    }

    return $tree;
  }

?>