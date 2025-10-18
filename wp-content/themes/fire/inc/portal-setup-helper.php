<?php
/**
 * Portal Setup Helper
 *
 * This file contains helper functions to set up the customer portal.
 * These are one-time setup functions and don't need to run on every page load.
 *
 * To use: Uncomment the line at the bottom to run the setup function once.
 */

/**
 * Create portal pages if they don't exist
 *
 * This will create the Assets and Profile pages with proper templates assigned
 */
function fire_create_portal_pages() {
  // Check if we're in admin
  if (!is_admin()) {
    return;
  }

  // Check if pages already exist
  $assets_page = get_page_by_path('assets');
  $profile_page = get_page_by_path('profile');

  $created = array();

  // Create Assets page
  if (!$assets_page) {
    $assets_page_id = wp_insert_post(array(
      'post_title' => 'Assets',
      'post_name' => 'assets',
      'post_content' => '',
      'post_status' => 'publish',
      'post_type' => 'page',
      'page_template' => 'page-assets.php'
    ));

    if (!is_wp_error($assets_page_id)) {
      $created[] = 'Assets page created with ID: ' . $assets_page_id;
    }
  }

  // Create Profile page
  if (!$profile_page) {
    $profile_page_id = wp_insert_post(array(
      'post_title' => 'Profile',
      'post_name' => 'profile',
      'post_content' => '',
      'post_status' => 'publish',
      'post_type' => 'page',
      'page_template' => 'page-profile.php'
    ));

    if (!is_wp_error($profile_page_id)) {
      $created[] = 'Profile page created with ID: ' . $profile_page_id;
    }
  }

  // Show admin notice
  if (!empty($created)) {
    add_action('admin_notices', function() use ($created) {
      echo '<div class="notice notice-success is-dismissible">';
      echo '<p><strong>Portal Setup:</strong></p>';
      echo '<ul>';
      foreach ($created as $message) {
        echo '<li>' . esc_html($message) . '</li>';
      }
      echo '</ul>';
      echo '</div>';
    });
  }
}

/**
 * Create sample asset categories
 *
 * Creates the asset categories used in the design
 */
function fire_create_sample_asset_categories() {
  // Check if taxonomy exists
  if (!taxonomy_exists('asset-category')) {
    return;
  }

  $categories = array(
    'Advertising & Media' => 'Display ads, social media graphics, and promotional materials',
    'Display' => 'Display advertising materials',
    'Stickers' => 'Sticker designs and templates',
    'Print' => 'Print-ready materials and templates',
    'Social' => 'Social media graphics and templates',
    'Video' => 'Video assets and templates',
    'Audio' => 'Audio files and scripts',
    'Native Assets' => 'Native advertising materials',
    'Branding' => 'Brand guidelines and logos',
    'Logos' => 'Logo files in various formats',
  );

  $created = array();

  foreach ($categories as $name => $description) {
    // Check if term exists
    $term = term_exists($name, 'asset-category');

    if (!$term) {
      $result = wp_insert_term($name, 'asset-category', array(
        'description' => $description,
        'slug' => sanitize_title($name),
      ));

      if (!is_wp_error($result)) {
        $created[] = $name;
      }
    }
  }

  if (!empty($created)) {
    add_action('admin_notices', function() use ($created) {
      echo '<div class="notice notice-success is-dismissible">';
      echo '<p><strong>Asset Categories Created:</strong> ' . implode(', ', $created) . '</p>';
      echo '</div>';
    });
  }
}

/**
 * Run the portal setup
 *
 * UNCOMMENT THE LINE BELOW TO RUN THE SETUP ONCE
 * Then comment it back out or delete this file after setup is complete
 */
// Uncomment to auto-create portal pages on next admin page load:
add_action('admin_init', 'fire_create_portal_pages');

// Uncomment to auto-create asset categories on next admin page load:
add_action('admin_init', 'fire_create_sample_asset_categories');

