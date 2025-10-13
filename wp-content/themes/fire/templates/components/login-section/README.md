# Login Section

A full-width section with media background (image or Vimeo video), login form with password reset functionality.

## Features

- **Media Background**: Choose between an image or Vimeo video background
- **WYSIWYG Content**: Flexible content area above the forms
- **Login Form**: Standard WordPress login form with email and password fields
- **Password Reset**: Toggle view to show password reset form (uses Alpine.js)
- **Sign Up Link**: Optional configurable sign up link

## ACF Fields

- **Media Type**: Button group to choose between Image or Vimeo Video
- **Image**: Image field (conditional on media type)
- **Vimeo Video ID**: Text field for Vimeo video ID (conditional on media type)
- **Copy**: WYSIWYG editor for content
- **Sign Up Link**: Link field for the sign up button

## Technical Details

### Form Styling

The forms follow the same CSS patterns as Gravity Forms in `theme/assets/styles/components/forms.css`:

- Transparent backgrounds with bottom borders
- Focus states with light-blue accent color
- Full-width layout with consistent spacing
- White text for dark backgrounds

### Alpine.js Integration

The password reset functionality uses Alpine.js to toggle between login and reset views:

- `x-data="{ showReset: false }"` - Initializes the toggle state
- `x-show="!showReset"` - Controls login form visibility
- `x-show="showReset"` - Controls reset form visibility
- `@click="showReset = true/false"` - Toggle buttons
- `x-cloak` - Prevents flash of unstyled content

### WordPress Integration

- Login form posts to `wp_login_url()`
- Password reset form posts to `wp_lostpassword_url()`
- Forms use standard WordPress authentication handling

## Layout Structure

The section uses a grid-stack layout similar to the home-hero component:

1. Background media layer
2. Gradient overlay layer
3. Content layer with forms

Forms are centered and constrained to `lg:col-[col-3/col-10] xl:col-[col-4/col-9]` for optimal readability.

## Notes

- Alpine.js is already included in the theme via `theme/main.js`
- The section automatically converts `login_section` (ACF layout name) to `login-section` (file path)
- All styles have been compiled into `dist/styles.css`
