# Customer Portal Setup Guide

This document explains the customer portal implementation for the Mutuals Matter website.

## Overview

The customer portal provides a secure area where registered users can:

- View and download marketing assets (if their account is active)
- Edit their profile information
- View their account status
- Contact support
- Log out

## Files Created

### Page Templates

- **`page-assets.php`** - Assets portal page (Template Name: "Assets Portal")
- **`page-profile.php`** - Profile edit page (Template Name: "Profile Portal")

### Components

- **`templates/components/portal-nav/portal-nav.php`** - Portal navigation bar

### Styles

- **`theme/assets/styles/components/portal.css`** - Portal-specific CSS

### User Fields

- **`acf-json/group_671234567890a.json`** - Updated with Bank Affiliation field

### Functions

- **`inc/fire-customer-status.php`** - Customer status and user field functions

## Setup Instructions

### 1. Sync ACF Fields

1. Log into WordPress admin
2. Go to **Custom Fields → Sync**
3. Sync the "Customer Status" field group
4. This will add the Bank Affiliation field to user profiles

### 2. Create Pages

Create two new pages in WordPress:

#### Assets Page

- **Title**: Assets
- **Slug**: `assets`
- **Template**: Assets Portal
- **Publish** the page

#### Profile Page

- **Title**: Profile
- **Slug**: `profile`
- **Template**: Profile Portal
- **Publish** the page

### 3. Compile CSS

Run your build process to compile the new portal styles:

```bash
npm run build
```

### 4. Test the Portal

1. **Create a test user**:

   - Go to Users → Add New
   - Fill in required information
   - By default, new users are active customers

2. **Log in as test user**:

   - Visit `/assets` - should see assets (if active)
   - Visit `/profile` - should see profile edit form

3. **Test inactive state**:
   - As admin, edit the user
   - Toggle "Active Customer" to OFF
   - Log in as that user
   - Visit `/assets` - should see "Access Restricted" message

## Portal Navigation

The portal navigation appears at the top of all portal pages with these items:

- **Logo** - Links to home page
- **Assets** - Only visible to active customers and admins
- **Profile** - Visible to all logged-in users
- **Help** - mailto link to `join@mutualbanksmatter.com`
- **Log out** - Logs user out and redirects to home page

The navigation is responsive with a mobile menu that uses Alpine.js for toggle functionality.

## Access Control

### Assets Page

- **Not logged in** → Redirects to login page
- **Logged in + Inactive** → Shows "Contact Help" message
- **Logged in + Active** → Shows all assets organized by category
- **Admins** → Always have access regardless of active status

### Profile Page

- **Not logged in** → Redirects to login page
- **Logged in** → Can edit profile regardless of active status

## User Profile Fields

The profile page includes these fields:

### Personal Information

- **Name** - Display name
- **Email** - User email (validated)
- **Phone Number** - Contact phone
- **Bank Affiliation** - ACF field for mutual bank name

### Preferences

- **Asset Updates Consent** - Checkbox for email notifications

### Password

- **New Password** - Optional password change
- **Confirm Password** - Must match new password

## Assets Display

Assets are organized by category (taxonomy: `asset-category`). For each asset:

### Asset Card Shows:

- **Preview Image** - Thumbnail with hover overlay
- **Title** - Asset name
- **File Type & Size** - e.g., "PDF · 2.5 MB"
- **Download Button** - Primary action
- **Preview Icon** - Opens full-size image in new tab (on hover)

### Asset Fields (ACF):

- **Category** - Taxonomy select (asset-category)
- **Preview Image** - Image field
- **Download File** - File field (any type)

## Account Status Indicator

The profile page displays the user's current status:

### Active Account

- Green badge with checkmark
- Shows expiration date if set
- Warning if expiring within 30 days

### Inactive Account

- Gray badge with X icon
- "Inactive" text

## Customization

### Change Portal Colors

Edit `theme/assets/styles/components/portal.css`:

```css
.portal-nav {
  @apply bg-gray-900; /* Change nav background */
}

.portal-nav__link.active {
  @apply text-blue-300; /* Change active link color */
}
```

### Change Help Email

Edit `templates/components/portal-nav/portal-nav.php`:

```php
<a href="mailto:YOUR-EMAIL@example.com?subject=Help">
```

### Change Logout Redirect

Edit `templates/components/portal-nav/portal-nav.php`:

```php
<a href="<?php echo wp_logout_url(home_url('/custom-page')); ?>">
```

## Adding Assets

### 1. Create Asset Categories

1. Go to **Assets → Categories** (or create the taxonomy first)
2. Add categories like:
   - Advertising & Media
   - Branding
   - Print Materials
   - etc.

### 2. Add Assets

1. Go to **Assets → Add New**
2. Enter asset title
3. Select category
4. Upload preview image
5. Upload download file
6. Publish

### 3. Assets Will Appear

- Visit the `/assets` page
- Assets are grouped by category
- Sorted alphabetically by title

## User Management

### Activating/Deactivating Users

**In WordPress Admin:**

1. Go to **Users**
2. See "Active Customer" column with status indicators
3. Edit user
4. Scroll to "Customer Status" section
5. Toggle "Active Customer" on/off
6. Optionally set "Active Till" date for auto-expiration
7. Update user

### Bulk User Management

Use the helper functions for programmatic management:

```php
// Activate multiple users after payment
$user_ids = [1, 2, 3, 4, 5];
foreach ($user_ids as $user_id) {
  // 1 year access
  fire_set_customer_active($user_id, date('Y-m-d', strtotime('+1 year')));
}

// Deactivate expired users
$users = get_users();
foreach ($users as $user) {
  if (!fire_is_active_customer($user->ID)) {
    // User is inactive, send notification email
  }
}
```

## Security Notes

1. **Nonce Protection** - Profile form uses WordPress nonces
2. **Permission Checks** - Users can only edit their own profile
3. **Email Validation** - Email addresses are validated and checked for duplicates
4. **Sanitization** - All inputs are sanitized before saving
5. **Login Required** - All portal pages redirect if not logged in

## Responsive Design

The portal is fully responsive:

- **Mobile** - Hamburger menu for navigation
- **Tablet** - 2-column asset grid
- **Desktop** - 3-column asset grid

## Alpine.js Integration

The portal uses Alpine.js for interactive features:

- Mobile menu toggle
- Click-away to close menu
- No jQuery required

Alpine.js is already included in the theme via `theme/main.js`.

## Troubleshooting

### Assets Don't Show

1. Check if asset categories exist
2. Check if assets are published
3. Verify assets have a category assigned
4. Check if user is active (or admin)

### Profile Won't Update

1. Check for PHP errors in debug log
2. Verify nonce is valid (may need to refresh page)
3. Check user permissions
4. Check email isn't already in use

### Navigation Doesn't Work

1. Run `npm run build` to compile CSS
2. Clear browser cache
3. Check that Alpine.js is loaded (view source)
4. Check console for JavaScript errors

### Styles Not Applied

1. Run `npm run build`
2. Clear WordPress cache (if using caching plugin)
3. Hard refresh browser (Cmd+Shift+R or Ctrl+Shift+F5)
4. Check that `portal.css` import is in `main.css`

## Future Enhancements

Potential additions for the portal:

- Asset favorites/bookmarks
- Download history tracking
- Asset search and filtering
- User notifications for new assets
- Asset preview modal instead of new tab
- Batch download feature
- Asset request form
- Usage analytics

## Support

For questions or issues:

- **Email**: join@mutualbanksmatter.com
- **Subject**: Portal Support
