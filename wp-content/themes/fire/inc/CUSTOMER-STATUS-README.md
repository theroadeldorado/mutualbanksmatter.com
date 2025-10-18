# Customer Status Management

This feature allows you to manage customer active status through ACF fields on user profiles.

## Features

- **Active Customer Toggle**: Simple on/off switch to activate/deactivate customer access
- **Active Till Date**: Optional expiration date for automatic access expiration
- **Indefinite Access**: Leave the date field blank for permanent active status
- **Helper Functions**: Easy-to-use PHP functions for checking customer status
- **Admin Column**: Visual status indicator in the WordPress users list

## How to Use

### Setting Up Customer Access (Admin)

1. Go to **Users** in the WordPress admin
2. Edit the user you want to manage
3. Scroll to the **Customer Status** section
4. Toggle **Active Customer** to activate the user
5. (Optional) Set an **Active Till** date for automatic expiration
   - Leave blank for indefinite access
   - Set a date for time-limited access

### Default Behavior

- New users are **active by default** with indefinite access
- When toggled off, the user becomes inactive immediately
- When an expiration date passes, the user automatically becomes inactive

### Using in Templates

#### Check if Current User is Active

```php
<?php if (fire_is_active_customer()): ?>
  <!-- Show customer content -->
  <div class="customer-content">
    <h2>Welcome, Active Customer!</h2>
  </div>
<?php else: ?>
  <!-- Show message for inactive users -->
  <p>Please activate your account to access this content.</p>
<?php endif; ?>
```

#### Check Specific User

```php
<?php
$user_id = 123; // Specific user ID
if (fire_is_active_customer($user_id)) {
  echo "User $user_id is active!";
}
?>
```

#### Get Expiration Information

```php
<?php
// Get expiration date
$expiration = fire_get_customer_expiration_date();
if ($expiration) {
  echo "Your access expires on: " . date('F j, Y', strtotime($expiration));
}

// Get days remaining
$days = fire_get_days_until_expiration();
if ($days !== false) {
  echo "You have $days days remaining.";
} else {
  echo "You have indefinite access.";
}
?>
```

#### Require Active Status (with Redirect)

```php
<?php
// Redirect inactive users to home page
fire_require_active_customer(home_url());

// Or just return true/false without redirect
if (fire_require_active_customer()) {
  // User is active, continue
}
?>
```

### Programmatically Managing Status

#### Activate a Customer

```php
<?php
// Activate with indefinite access
fire_set_customer_active($user_id);

// Activate with expiration date
fire_set_customer_active($user_id, '2025-12-31');
?>
```

#### Deactivate a Customer

```php
<?php
fire_set_customer_inactive($user_id);
?>
```

## Available Functions

| Function                                      | Description                       | Returns         |
| --------------------------------------------- | --------------------------------- | --------------- |
| `fire_is_active_customer($user_id)`           | Check if user is currently active | `bool`          |
| `fire_get_customer_expiration_date($user_id)` | Get expiration date               | `string\|false` |
| `fire_get_days_until_expiration($user_id)`    | Get days until expiration         | `int\|false`    |
| `fire_set_customer_active($user_id, $date)`   | Activate a customer               | `bool`          |
| `fire_set_customer_inactive($user_id)`        | Deactivate a customer             | `bool`          |
| `fire_require_active_customer($redirect_url)` | Require active status             | `bool`          |

_Note: All functions default to current user if `$user_id` is not provided_

## Admin Users List

The WordPress users list now includes an **Active Customer** column showing:

- **● Active (indefinite)** - Green, no expiration date
- **● Active (till Date)** - Green, expires in more than 7 days
- **● Active (expires Date)** - Yellow/Orange, expires within 7 days
- **● Expired** - Red, expiration date has passed
- **○ Inactive** - Gray, manually deactivated

## Integration Examples

### Restrict Page Template to Active Customers

```php
<?php
// At the top of your page template
if (!fire_require_active_customer(home_url())) {
  return;
}

// Rest of your template code
?>
```

### Show Different Content Based on Status

```php
<?php if (is_user_logged_in()): ?>
  <?php if (fire_is_active_customer()): ?>
    <!-- Active customer content -->
    <?php
    $days = fire_get_days_until_expiration();
    if ($days !== false && $days <= 30): ?>
      <div class="expiration-notice">
        Your subscription expires in <?php echo $days; ?> days.
      </div>
    <?php endif; ?>
  <?php else: ?>
    <!-- Inactive customer content -->
    <div class="inactive-notice">
      <p>Your account is currently inactive. Please contact us to reactivate.</p>
    </div>
  <?php endif; ?>
<?php endif; ?>
```

### Custom Menu Item Visibility

```php
<?php
add_filter('wp_nav_menu_items', function($items, $args) {
  if ($args->theme_location == 'primary' && fire_is_active_customer()) {
    $items .= '<li class="menu-item"><a href="/customer-portal">Customer Portal</a></li>';
  }
  return $items;
}, 10, 2);
?>
```

## Payment Integration Hook Example

```php
<?php
// Example: Activate customer after successful payment
add_action('payment_complete', function($user_id, $subscription_length) {
  // Calculate expiration date (e.g., 1 year from now)
  $expiration_date = date('Y-m-d', strtotime('+' . $subscription_length . ' months'));

  // Activate customer with expiration date
  fire_set_customer_active($user_id, $expiration_date);
}, 10, 2);
?>
```

## Notes

- The feature requires Advanced Custom Fields (ACF) plugin to be active
- All dates are stored in `Y-m-d` format (e.g., `2025-12-31`)
- Date comparisons use WordPress server timezone
- Users are active by default to allow for immediate access upon payment
