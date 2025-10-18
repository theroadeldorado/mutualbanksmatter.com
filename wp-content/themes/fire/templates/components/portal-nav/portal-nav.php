<?php
/**
 * Customer Portal Navigation
 *
 * Shows navigation for the customer portal with Assets, Profile, Help, and Logout
 */

$current_user = wp_get_current_user();
$is_active_customer = fire_is_active_customer();
$is_admin = current_user_can('manage_options');

// Get current page URL to determine active nav item
$current_url = home_url($_SERVER['REQUEST_URI']);
$assets_url = home_url('/assets');
$profile_url = home_url('/profile');
?>

<nav class="text-white mb-12">
    <ul class="flex items-center gap-8">
      <?php if ($is_active_customer || $is_admin): ?>
      <li>
        <a href="<?php echo $assets_url; ?>"
            class="text-white no-underline hover:text-light-blue font-medium <?php echo (strpos($current_url, '/assets') !== false) ? 'text-light-blue' : ''; ?>">
          Assets
        </a>
      </li>
      <?php endif; ?>

      <li>
        <a href="<?php echo $profile_url; ?>"
            class="text-white no-underline hover:text-light-blue font-medium <?php echo (strpos($current_url, '/profile') !== false) ? 'text-light-blue' : ''; ?>">
          Profile
        </a>
      </li>

      <li>
        <a href="mailto:join@mutualbanksmatter.com?subject=Help"
            class="text-white no-underline hover:text-light-blue font-medium">
          Help
        </a>
      </li>

      <li>
        <a href="<?php echo wp_logout_url(home_url()); ?>"
            class="text-white no-underline hover:text-light-blue font-medium">
          Log out
        </a>
      </li>
    </ul>
</nav>

