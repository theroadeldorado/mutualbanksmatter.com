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
 * Display setup instructions in admin
 */
function fire_portal_setup_admin_notice() {
  $screen = get_current_screen();

  // Only show on dashboard
  if ($screen->id !== 'dashboard') {
    return;
  }

  // Check if pages exist
  $assets_page = get_page_by_path('assets');
  $profile_page = get_page_by_path('profile');

  if (!$assets_page || !$profile_page) {
    ?>
    <div class="notice notice-info">
      <h2>Customer Portal Setup</h2>
      <p>The customer portal has been installed but needs pages created.</p>
      <p><strong>Next Steps:</strong></p>
      <ol>
        <li>Go to <strong>Custom Fields → Sync</strong> to sync the updated user fields</li>
        <li>Create two pages:
          <ul>
            <li><strong>Assets</strong> (slug: assets) with template "Assets Portal"</li>
            <li><strong>Profile</strong> (slug: profile) with template "Profile Portal"</li>
          </ul>
        </li>
        <li>Run <code>npm run build</code> to compile the portal styles</li>
        <li>See <code>PORTAL-SETUP.md</code> for complete documentation</li>
      </ol>
    </div>
    <?php
  }
}
// Uncomment to show setup notice on dashboard:
// add_action('admin_notices', 'fire_portal_setup_admin_notice');

/**
 * Run the portal setup
 *
 * UNCOMMENT THE LINE BELOW TO RUN THE SETUP ONCE
 * Then comment it back out or delete this file after setup is complete
 */
// Uncomment to auto-create portal pages on next admin page load:
// add_action('admin_init', 'fire_create_portal_pages');

// Uncomment to auto-create asset categories on next admin page load:
// add_action('admin_init', 'fire_create_sample_asset_categories');

