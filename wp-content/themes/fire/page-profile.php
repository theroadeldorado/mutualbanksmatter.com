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
$message = '';
$message_type = '';

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
      $message = 'Error updating profile: ' . $updated->get_error_message();
      $message_type = 'error';
    } else {
      $message = 'Profile updated successfully!';
      $message_type = 'success';

      // Refresh user data
      $current_user = wp_get_current_user();
    }
  } else {
    $message = implode('<br>', $errors);
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
    <div class="px-8 lg:px-12">
      <?php get_template_part('templates/components/portal-nav/portal-nav'); ?>

      <div class="max-w-3xl mx-auto">

        <h1 class="text-4xl lg:text-5xl font-bold mb-8">Your profile</h1>

        <?php if ($message): ?>
          <div class="p-4 mb-6 rounded-lg font-medium border <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200'; ?>">
            <?php echo wp_kses_post($message); ?>
          </div>
        <?php endif; ?>

        <!-- Account Status Card -->
        <div class="mb-8 p-6 rounded-lg border-2 transition-all duration-300 <?php echo $is_active ? 'border-green-400' : 'border-red-400'; ?>">
          <h2 class="text-xl font-semibold mb-3 text-white">Account Status</h2>

          <div class="flex items-center gap-3 mb-2">
            <?php if ($is_active): ?>
              <span class="inline-flex items-center gap-2 text-green-400 font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Active Customer
              </span>
            <?php else: ?>
              <span class="inline-flex items-center gap-2 text-red-400 font-medium">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                Inactive
              </span>
            <?php endif; ?>
          </div>

          <?php if ($is_active && $expiration_date): ?>
            <p class="text-sm <?php echo ($days_remaining !== false && $days_remaining <= 30) ? 'text-orange-400' : 'text-white'; ?>">
              <?php if ($days_remaining === 0): ?>
                Your access has expired.
              <?php elseif ($days_remaining !== false && $days_remaining <= 7): ?>
                <strong>Expires soon!</strong> Your access expires on <?php echo date('F j, Y', strtotime($expiration_date)); ?> (<?php echo $days_remaining; ?> days remaining)
              <?php else: ?>
                Active until <?php echo date('F j, Y', strtotime($expiration_date)); ?>
              <?php endif; ?>
            </p>
          <?php elseif ($is_active): ?>
            <p class="text-sm text-white">Indefinite access</p>
          <?php endif; ?>
        </div>

        <!-- Profile Form -->
        <form method="post" class="space-y-6 p-8 rounded-lg border-2 border-white">
          <?php wp_nonce_field('update_profile_action', 'profile_nonce'); ?>

          <div class="pb-6">
            <h2 class="text-2xl font-semibold mb-4 text-white">Personal Information</h2>

            <div class="mb-6">
              <label for="display_name" class="block text-base text-white mb-1">
                Name
              </label>
              <input type="text"
                     id="display_name"
                     name="display_name"
                     value="<?php echo esc_attr($display_name); ?>"
                     class="bg-transparent border-0 border-b-2 border-white py-2 px-0 w-full text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue">
            </div>

            <div class="mb-6">
              <label for="email" class="block text-base text-white mb-1">
                Email
              </label>
              <input type="email"
                     id="email"
                     name="email"
                     value="<?php echo esc_attr($email); ?>"
                     required
                     class="bg-transparent border-0 border-b-2 border-white py-2 px-0 w-full text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue">
            </div>

            <div class="mb-6">
              <label for="phone" class="block text-base text-white mb-1">
                Phone Number
              </label>
              <input type="tel"
                     id="phone"
                     name="phone"
                     value="<?php echo esc_attr($phone); ?>"
                     class="bg-transparent border-0 border-b-2 border-white py-2 px-0 w-full text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue">
            </div>

            <div class="mb-6">
              <label for="bank_affiliation" class="block text-base text-white mb-1">
                Bank Affiliation
              </label>
              <input type="text"
                     id="bank_affiliation"
                     name="bank_affiliation"
                     value="<?php echo esc_attr($bank_affiliation); ?>"
                     placeholder="e.g., Mutual Bank"
                     class="bg-transparent border-0 border-b-2 border-white py-2 px-0 w-full text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue">
            </div>
          </div>

          <div class="pb-6">
            <h2 class="text-2xl font-semibold mb-4 text-white">Preferences</h2>

            <div class="mb-6">
              <label class="flex items-start gap-3 cursor-pointer text-white">
                <input type="checkbox"
                       name="asset_updates"
                       value="1"
                       <?php checked($asset_updates_consent, '1'); ?>
                       class="mt-1 w-4 h-4 text-light-blue border-white rounded focus:ring-light-blue bg-transparent">
                <span class="text-base">I consent to receive asset update emails.</span>
              </label>
            </div>
          </div>

          <div class="pb-6">
            <h2 class="text-2xl font-semibold mb-4 text-white">Change Password</h2>
            <p class="text-sm text-gray-400 mb-4">Leave blank to keep your current password</p>

            <div class="mb-6">
              <label for="new_password" class="block text-base text-white mb-1">
                New Password
              </label>
              <input type="password"
                     id="new_password"
                     name="new_password"
                     autocomplete="new-password"
                     class="bg-transparent border-0 border-b-2 border-white py-2 px-0 w-full text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue">
            </div>

            <div class="mb-6">
              <label for="confirm_password" class="block text-base text-white mb-1">
                Confirm New Password
              </label>
              <input type="password"
                     id="confirm_password"
                     name="confirm_password"
                     autocomplete="new-password"
                     class="bg-transparent border-0 border-b-2 border-white py-2 px-0 w-full text-lg text-white placeholder:text-gray-400 focus:outline-none focus:shadow-none focus:border-b-light-blue">
            </div>
          </div>

          <div class="flex gap-4">
            <button type="submit"
                    name="update_profile"
                    class="button button-light-blue">
              Save Changes
            </button>

            <a href="<?php echo home_url('/assets'); ?>"
               class="button button-light-blue">
              Cancel
            </a>
          </div>
        </form>

        <!-- Password Reset Link -->
        <div class="mt-6 text-center">
          <a href="<?php echo wp_lostpassword_url(get_permalink()); ?>"
             class="text-sm text-white hover:text-light-blue underline">
            Reset password via email
          </a>
        </div>

      </div>
    </div>
  </main>
<?php get_footer(); ?>

