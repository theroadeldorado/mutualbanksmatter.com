<?php
/**
 * Customer Status Usage Examples
 *
 * This file contains example code snippets showing how to use
 * the customer status functionality. This file is not automatically
 * included - it's for reference only.
 *
 * Delete this file or keep it for reference.
 */

// Example 1: Basic customer content restriction
function example_show_customer_only_content() {
  if (fire_is_active_customer()) {
    echo '<div class="customer-content">';
    echo '<h2>Exclusive Customer Content</h2>';
    echo '<p>Welcome! Here is your premium content.</p>';
    echo '</div>';
  } else {
    echo '<div class="inactive-message">';
    echo '<p>Please activate your account to view this content.</p>';
    echo '</div>';
  }
}

// Example 2: Show expiration warning
function example_show_expiration_warning() {
  if (!fire_is_active_customer()) {
    return;
  }

  $days_remaining = fire_get_days_until_expiration();

  // Only show warning if there's an expiration date and it's within 30 days
  if ($days_remaining !== false && $days_remaining <= 30) {
    echo '<div class="expiration-warning">';

    if ($days_remaining === 0) {
      echo '<p>Your access has expired. Please renew to continue.</p>';
    } elseif ($days_remaining <= 7) {
      echo '<p><strong>Warning:</strong> Your access expires in ' . $days_remaining . ' days!</p>';
    } else {
      echo '<p>Your access expires in ' . $days_remaining . ' days.</p>';
    }

    echo '</div>';
  }
}

// Example 3: Redirect non-active users
function example_protect_customer_page() {
  // Use at the top of a template file
  if (!fire_require_active_customer(home_url())) {
    // User will be redirected to home if not active
    return;
  }

  // Continue with page content...
}

// Example 4: Custom shortcode for customer content
function example_customer_content_shortcode($atts, $content = null) {
  if (!fire_is_active_customer()) {
    return '<p>This content is only available to active customers.</p>';
  }

  return do_shortcode($content);
}
// To use: add_shortcode('customer_only', 'example_customer_content_shortcode');
// Then in content: [customer_only]Premium content here[/customer_only]

// Example 5: Show different menus based on status
function example_custom_menu_for_customers($items, $args) {
  // Only modify the primary menu
  if ($args->theme_location !== 'primary') {
    return $items;
  }

  if (fire_is_active_customer()) {
    // Add customer portal link for active customers
    $items .= '<li class="menu-item customer-portal">';
    $items .= '<a href="/customer-portal">Customer Portal</a>';
    $items .= '</li>';
  } else {
    // Add activation link for inactive users
    $items .= '<li class="menu-item activate-account">';
    $items .= '<a href="/activate">Activate Account</a>';
    $items .= '</li>';
  }

  return $items;
}
// To use: add_filter('wp_nav_menu_items', 'example_custom_menu_for_customers', 10, 2);

// Example 6: Payment webhook handler
function example_payment_webhook_handler($user_id, $payment_data) {
  // Example: Activate user for 1 year after payment

  // Calculate expiration (1 year from now)
  $months = 12;
  $expiration_date = date('Y-m-d', strtotime('+' . $months . ' months'));

  // Activate the customer
  fire_set_customer_active($user_id, $expiration_date);

  // Optionally send confirmation email
  $user = get_user_by('id', $user_id);
  wp_mail(
    $user->user_email,
    'Account Activated',
    'Your account has been activated until ' . date('F j, Y', strtotime($expiration_date))
  );
}

// Example 7: Admin bulk action to activate users
function example_bulk_activate_customers($user_ids, $months = 12) {
  foreach ($user_ids as $user_id) {
    $expiration_date = date('Y-m-d', strtotime('+' . $months . ' months'));
    fire_set_customer_active($user_id, $expiration_date);
  }
}

// Example 8: AJAX handler for customer status check
function example_ajax_check_customer_status() {
  check_ajax_referer('customer_status_nonce', 'nonce');

  $response = array(
    'is_active' => fire_is_active_customer(),
    'expiration_date' => fire_get_customer_expiration_date(),
    'days_remaining' => fire_get_days_until_expiration()
  );

  wp_send_json_success($response);
}
// To use: add_action('wp_ajax_check_customer_status', 'example_ajax_check_customer_status');

// Example 9: Display customer status in account dashboard
function example_account_status_widget() {
  if (!is_user_logged_in()) {
    return;
  }

  echo '<div class="account-status-widget">';
  echo '<h3>Account Status</h3>';

  if (fire_is_active_customer()) {
    echo '<p class="status-active">✓ Active</p>';

    $expiration = fire_get_customer_expiration_date();
    if ($expiration) {
      $days = fire_get_days_until_expiration();
      echo '<p>Expires: ' . date('F j, Y', strtotime($expiration)) . '</p>';

      if ($days <= 30) {
        echo '<p class="warning">Renew soon! Only ' . $days . ' days remaining.</p>';
        echo '<a href="/renew" class="button">Renew Now</a>';
      }
    } else {
      echo '<p>Indefinite Access</p>';
    }
  } else {
    echo '<p class="status-inactive">✗ Inactive</p>';
    echo '<p>Activate your account to access customer features.</p>';
    echo '<a href="/activate" class="button">Activate Now</a>';
  }

  echo '</div>';
}

// Example 10: Scheduled task to send expiration reminders
function example_send_expiration_reminders() {
  // Get all users
  $users = get_users(array('fields' => 'ID'));

  foreach ($users as $user_id) {
    if (!fire_is_active_customer($user_id)) {
      continue;
    }

    $days = fire_get_days_until_expiration($user_id);

    // Send reminder if expiring in exactly 7 days
    if ($days === 7) {
      $user = get_user_by('id', $user_id);
      $expiration = fire_get_customer_expiration_date($user_id);

      wp_mail(
        $user->user_email,
        'Your account expires in 7 days',
        'Your account will expire on ' . date('F j, Y', strtotime($expiration)) . '. Please renew to continue access.'
      );
    }
  }
}
// To use: Set up a daily cron job
// add_action('send_expiration_reminders_event', 'example_send_expiration_reminders');

