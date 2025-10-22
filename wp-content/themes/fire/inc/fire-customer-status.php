<?php
/**
 * Customer Status Helper Functions
 *
 * Functions to check and manage customer active status
 * based on ACF fields on user profiles.
 */

/**
 * Check if a user is currently an active customer
 *
 * This checks both the active_customer toggle and the active_till date
 * to determine if a user should have active access.
 *
 * @param int|null $user_id User ID to check. Defaults to current user.
 * @return bool True if user is active, false otherwise
 */
function fire_is_active_customer($user_id = null) {
  // Default to current user if no user_id provided
  if ($user_id === null) {
    $user_id = get_current_user_id();
  }

  // If no user_id available, return false
  if (!$user_id) {
    return false;
  }

  // Check if active_customer toggle is enabled
  // ACF true/false fields can return 1, "1", true, or false
  $is_active = get_field('active_customer', 'user_' . $user_id);

  // Convert to boolean - handles 1, "1", true
  $is_active = filter_var($is_active, FILTER_VALIDATE_BOOLEAN);

  // If not active, return false
  if (!$is_active) {
    return false;
  }

  // Check if there's an expiration date
  $active_till = get_field('active_till', 'user_' . $user_id);

  // If no expiration date, user is active indefinitely
  if (empty($active_till)) {
    return true;
  }

  // Compare expiration date with today using WordPress timezone
  $today = current_time('Y-m-d');

  // User is active if today is before or equal to the expiration date
  // User becomes inactive the day AFTER the expiration date
  return $today <= $active_till;
}

/**
 * Get the customer's expiration date
 *
 * @param int|null $user_id User ID to check. Defaults to current user.
 * @return string|false The expiration date in Y-m-d format, or false if none set
 */
function fire_get_customer_expiration_date($user_id = null) {
  if ($user_id === null) {
    $user_id = get_current_user_id();
  }

  if (!$user_id) {
    return false;
  }

  return get_field('active_till', 'user_' . $user_id);
}

/**
 * Get the number of days until customer access expires
 *
 * @param int|null $user_id User ID to check. Defaults to current user.
 * @return int|false Number of days remaining, 0 if expired, false if no expiration set or indefinite
 */
function fire_get_days_until_expiration($user_id = null) {
  if ($user_id === null) {
    $user_id = get_current_user_id();
  }

  if (!$user_id) {
    return false;
  }

  $active_till = get_field('active_till', 'user_' . $user_id);

  // If no expiration date, return false (indefinite)
  if (empty($active_till)) {
    return false;
  }

  // Use WordPress timezone for consistent date handling
  $today = new DateTime(current_time('Y-m-d'));
  $expiration = new DateTime($active_till);
  $interval = $today->diff($expiration);

  // If expired (negative), return 0
  if ($interval->invert) {
    return 0;
  }

  return (int) $interval->days;
}

/**
 * Set a user as an active customer
 *
 * @param int $user_id User ID
 * @param string|null $active_till Optional expiration date in Y-m-d format
 * @return bool True on success
 */
function fire_set_customer_active($user_id, $active_till = null) {
  if (!$user_id) {
    return false;
  }

  // Set active status
  update_field('active_customer', true, 'user_' . $user_id);

  // Set expiration date if provided
  if ($active_till !== null) {
    update_field('active_till', $active_till, 'user_' . $user_id);
  }

  return true;
}

/**
 * Set a user as inactive
 *
 * @param int $user_id User ID
 * @return bool True on success
 */
function fire_set_customer_inactive($user_id) {
  if (!$user_id) {
    return false;
  }

  return update_field('active_customer', false, 'user_' . $user_id);
}

/**
 * Restrict content to active customers only
 * Can be used as a shortcode wrapper or in templates
 *
 * Usage in templates:
 * if (fire_require_active_customer()) {
 *   // Show customer content
 * }
 *
 * @param string $redirect_url Optional URL to redirect to if not active. Default: home
 * @return bool True if user is active, false otherwise
 */
function fire_require_active_customer($redirect_url = null) {
  if (!is_user_logged_in()) {
    if ($redirect_url) {
      wp_redirect($redirect_url);
      exit;
    }
    return false;
  }

  if (!fire_is_active_customer()) {
    if ($redirect_url) {
      wp_redirect($redirect_url);
      exit;
    }
    return false;
  }

  return true;
}

/**
 * Add user list column to show active status
 */
function fire_add_active_customer_column($columns) {
  $columns['active_customer'] = 'Active Customer';
  return $columns;
}
add_filter('manage_users_columns', 'fire_add_active_customer_column');

/**
 * Display active status in user list column
 */
function fire_show_active_customer_column_content($value, $column_name, $user_id) {
  if ($column_name === 'active_customer') {
    if (fire_is_active_customer($user_id)) {
      $expiration = fire_get_customer_expiration_date($user_id);
      if ($expiration) {
        $days = fire_get_days_until_expiration($user_id);
        $formatted_date = date('M j, Y', strtotime($expiration));

        if ($days === 0) {
          return '<span style="color: #d63638;">● Expired</span>';
        } elseif ($days <= 7) {
          return '<span style="color: #dba617;">● Active (expires ' . $formatted_date . ')</span>';
        } else {
          return '<span style="color: #00a32a;">● Active (till ' . $formatted_date . ')</span>';
        }
      }
      return '<span style="color: #00a32a;">● Active (indefinite)</span>';
    }
    return '<span style="color: #8c8f94;">○ Inactive</span>';
  }
  return $value;
}
add_filter('manage_users_custom_column', 'fire_show_active_customer_column_content', 10, 3);

/**
 * Make the active customer column sortable
 */
function fire_make_active_customer_column_sortable($columns) {
  $columns['active_customer'] = 'active_customer';
  return $columns;
}
add_filter('manage_users_sortable_columns', 'fire_make_active_customer_column_sortable');

/**
 * Optional: Check user status after login and redirect inactive users
 * Uncomment the add_action below to enable this feature
 */
function fire_check_customer_status_on_login($user_login, $user) {
  // If user is not an active customer, redirect to a specific page
  if (!fire_is_active_customer($user->ID)) {
    // You can customize this redirect URL
    wp_redirect(home_url('/account-inactive'));
    exit;
  }
}
// Uncomment to enable automatic redirect of inactive users on login:
// add_action('wp_login', 'fire_check_customer_status_on_login', 10, 2);

/**
 * Set new users as inactive customers by default
 */
function fire_set_new_user_inactive($user_id) {
  // Set active_customer to false for new users
  update_field('active_customer', false, 'user_' . $user_id);
}
add_action('user_register', 'fire_set_new_user_inactive');

/**
 * Add phone number field to user profile in WordPress admin
 */
function fire_add_phone_field_to_user_profile($user) {
  ?>
  <h3>Contact Information</h3>
  <table class="form-table">
    <tr>
      <th><label for="phone">Phone Number</label></th>
      <td>
        <input type="text"
               name="phone"
               id="phone"
               value="<?php echo esc_attr(get_user_meta($user->ID, 'phone', true)); ?>"
               class="regular-text" />
        <p class="description">Enter the user's phone number.</p>
      </td>
    </tr>
  </table>
  <?php
}
add_action('show_user_profile', 'fire_add_phone_field_to_user_profile');
add_action('edit_user_profile', 'fire_add_phone_field_to_user_profile');

/**
 * Save phone number field when user profile is updated
 */
function fire_save_phone_field($user_id) {
  if (!current_user_can('edit_user', $user_id)) {
    return false;
  }

  if (isset($_POST['phone'])) {
    update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
  }
}
add_action('personal_options_update', 'fire_save_phone_field');
add_action('edit_user_profile_update', 'fire_save_phone_field');

