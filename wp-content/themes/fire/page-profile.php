<?php
/**
 * Template Name: Profile Portal
 *
 * Customer portal page for editing user profile
 */

get_header();

// Redirect to login if not logged in
if (!is_user_logged_in()) {
  wp_redirect(wp_login_url(get_permalink()));
  exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Handle form submission
if (isset($_POST['update_profile']) && wp_verify_nonce($_POST['profile_nonce'], 'update_profile_action')) {
  $errors = array();

  // Update basic user fields
  $user_data = array(
    'ID' => $user_id,
  );

  // Name
  if (isset($_POST['first_name'])) {
    $user_data['first_name'] = sanitize_text_field($_POST['first_name']);
  }

  if (isset($_POST['last_name'])) {
    $user_data['last_name'] = sanitize_text_field($_POST['last_name']);
  }

  // Display name
  if (isset($_POST['display_name'])) {
    $user_data['display_name'] = sanitize_text_field($_POST['display_name']);
  }

  // Email
  if (isset($_POST['email'])) {
    $email = sanitize_email($_POST['email']);
    if (is_email($email)) {
      // Check if email is already in use by another user
      $email_exists = email_exists($email);
      if ($email_exists && $email_exists != $user_id) {
        $errors[] = 'This email is already in use.';
      } else {
        $user_data['user_email'] = $email;
      }
    } else {
      $errors[] = 'Invalid email address.';
    }
  }

  // Phone
  if (isset($_POST['phone'])) {
    update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
  }

  // Bank Affiliation (ACF field)
  if (isset($_POST['bank_affiliation'])) {
    update_field('bank_affiliation', sanitize_text_field($_POST['bank_affiliation']), 'user_' . $user_id);
  }

  // Password change
  if (!empty($_POST['new_password'])) {
    if ($_POST['new_password'] === $_POST['confirm_password']) {
      $user_data['user_pass'] = $_POST['new_password'];
    } else {
      $errors[] = 'Passwords do not match.';
    }
  }

  // Asset update emails consent
  if (isset($_POST['asset_updates'])) {
    update_user_meta($user_id, 'asset_updates_consent', '1');
  } else {
    update_user_meta($user_id, 'asset_updates_consent', '0');
  }

  if (empty($errors)) {
    $updated = wp_update_user($user_data);

    if (is_wp_error($updated)) {
      // Redirect with error message
      wp_redirect(add_query_arg('profile_updated', 'error', get_permalink()));
      exit;
    } else {
      // Redirect with success message
      wp_redirect(add_query_arg('profile_updated', 'success', get_permalink()));
      exit;
    }
  } else {
    // Redirect with error message
    wp_redirect(add_query_arg(array('profile_updated' => 'error', 'error_msg' => urlencode(implode('<br>', $errors))), get_permalink()));
    exit;
  }
}

// Get message from URL parameters
$message = '';
$message_type = '';

if (isset($_GET['profile_updated'])) {
  if ($_GET['profile_updated'] === 'success') {
    $message = 'Profile updated successfully!';
    $message_type = 'success';
  } elseif ($_GET['profile_updated'] === 'error') {
    if (isset($_GET['error_msg'])) {
      $message = urldecode($_GET['error_msg']);
    } else {
      $message = 'Error updating profile. Please try again.';
    }
    $message_type = 'error';
  }
}

// Get user data
$first_name = $current_user->first_name;
$last_name = $current_user->last_name;
$display_name = $current_user->display_name;
$email = $current_user->user_email;
$phone = get_user_meta($user_id, 'phone', true);
$bank_affiliation = get_field('bank_affiliation', 'user_' . $user_id);
$asset_updates_consent = get_user_meta($user_id, 'asset_updates_consent', true);

// Get customer status info
$is_active = fire_is_active_customer();
$expiration_date = fire_get_customer_expiration_date();
$days_remaining = fire_get_days_until_expiration();
?>

  <main class="py-36 lg:py-40 site-main">
    <div class="fire-container">
      <?php get_template_part('templates/components/portal-nav/portal-nav'); ?>
      <div class="col-[main] md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-3/col-9] flex-col gap-6">
        <h1 class="text-4xl lg:text-5xl font-bold mb-6 shrink-0">Your profile</h1>
        <!-- account status -->
        <div class="py-2 px-4 mb-6 rounded-lg flex flex-col border-2 transition-all duration-300 <?php echo $is_active ? 'border-green-400 bg-green-400/10' : 'border-red-500 bg-red-500/10'; ?>">
          <div class="flex items-center gap-2">
            <div class="flex items-center gap-3">
              <?php if ($is_active): ?>
                <span class="inline-flex items-center gap-2 text-green-400 text-xl font-bold shrink-0">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Active Customer
                </span>
              <?php else: ?>
                <span class="inline-flex items-center gap-2 text-red-500 text-xl font-bold shrink-0">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                  </svg>
                  Inactive Customer
                </span>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($is_active && $expiration_date): ?>
            <p class="text-sm <?php echo ($days_remaining !== false && $days_remaining <= 30) ? 'text-white' : 'text-white'; ?>">
              <?php if ($days_remaining === 0): ?>
                Your access has expired.
              <?php elseif ($days_remaining !== false && $days_remaining <= 30): ?>
                <strong>Expires soon!</strong> Your access expires on <?php echo date('F j, Y', strtotime($expiration_date)); ?>
              <?php else: ?>
                Active until <?php echo date('F j, Y', strtotime($expiration_date)); ?>
              <?php endif; ?>
            </p>
          <?php elseif ($is_active): ?>
            <p class="text-sm text-white">Indefinite access</p>
          <?php else: ?>
            <p class="text-sm text-white">Your account is currently inactive. Please contact  <a href="mailto:<?php echo $help_email; ?>?subject=Help" class="no-underline text-white hover:text-light-blue">help</a> to activate your access to the assets portal.</p>

          <?php endif; ?>
        </div>
      </div>

      <div class="col-[main] md:col-[col-2/col-11] lg:col-[col-2/col-10] xl:col-[col-3/col-9]">
        <?php if ($message): ?>
          <div class="p-4 mb-6 rounded-lg font-medium border <?php echo $message_type === 'success' ? 'text-green-400 border-green-400 bg-green-400/10' : 'text-white border-red-500 bg-red-500/10'; ?>">
            <?php echo wp_kses_post($message); ?>
          </div>
        <?php endif; ?>

        <!-- Profile Form -->
        <form method="post" class="profile-form space-y-6 p-8 rounded-lg border-2 border-white">
          <?php wp_nonce_field('update_profile_action', 'profile_nonce'); ?>

          <div class="pb-6">
            <h2 class="text-2xl font-semibold mb-4 text-white">Personal Information</h2>

            <div class="form-field">
              <label for="display_name">Name</label>
              <input type="text"
                     id="display_name"
                     name="display_name"
                     value="<?php echo esc_attr($display_name); ?>">
            </div>

            <div class="form-field">
              <label for="email">Email</label>
              <input type="email"
                     id="email"
                     name="email"
                     value="<?php echo esc_attr($email); ?>"
                     required>
            </div>

            <div class="form-field">
              <label for="phone">Phone Number</label>
              <input type="tel"
                     id="phone"
                     name="phone"
                     value="<?php echo esc_attr($phone); ?>">
            </div>

            <div class="form-field">
              <label for="bank_affiliation">Bank Affiliation</label>
              <input type="text"
                     id="bank_affiliation"
                     name="bank_affiliation"
                     value="<?php echo esc_attr($bank_affiliation); ?>"
                     placeholder="e.g., Mutual Bank">
            </div>
          </div>

          <div class="pb-2">
            <h2 class="text-2xl font-semibold mb-4 text-white">Change Password</h2>
            <p class="text-sm text-white mb-4">Leave blank to keep your current password</p>

            <div class="form-field">
              <label for="new_password">New Password</label>
              <input type="password"
                     id="new_password"
                     name="new_password"
                     autocomplete="new-password">
            </div>

            <div class="form-field">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password"
                     id="confirm_password"
                     name="confirm_password"
                     autocomplete="new-password">
            </div>
          </div>

          <div class="flex gap-4">
            <button type="submit" name="update_profile" class="button">
              Save Changes
            </button>

            <a href="<?php echo home_url('/assets'); ?>" class="button">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </main>
<?php get_footer(); ?>

