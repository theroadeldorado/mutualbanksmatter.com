<?php
/**
 * Customer Portal Navigation
 *
 * Shows navigation for the customer portal with Assets, Profile, Help, and Logout
 */

$current_user = wp_get_current_user();
$is_active_customer = fire_is_active_customer();
$is_admin = current_user_can('manage_options');
$help_email = get_field('help_email', 'site_settings');
$current_url = rtrim(home_url($_SERVER['REQUEST_URI']), '/');
$assets_url = home_url('/assets');
$profile_url = home_url('/profile');
?>

<nav class="text-white mb-12">
  <ul class="flex justify-start gap-6 md:justify-end md:gap-8">
    <?php if ($is_active_customer || $is_admin): ?>
    <li>
      <a href="<?php echo $assets_url; ?>" class="text-white no-underline hover:text-light-blue font-medium <?php echo $current_url === $assets_url ? '!text-light-blue' : ''; ?>">
        Assets
      </a>
    </li>
    <?php endif; ?>

    <li>
      <a href="<?php echo $profile_url; ?>" class="text-white no-underline hover:text-light-blue font-medium <?php echo $current_url === $profile_url ? '!text-light-blue' : ''; ?>">
        Profile
      </a>
    </li>
    <?php if ($help_email): ?>
    <li>
      <a href="mailto:<?php echo $help_email; ?>?subject=Help" class="text-white no-underline hover:text-light-blue font-medium">
          Help
        </a>
      </li>
    <?php endif; ?>
    <li>
      <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-white no-underline hover:text-light-blue font-medium">
        Log out
      </a>
    </li>
  </ul>
</nav>

