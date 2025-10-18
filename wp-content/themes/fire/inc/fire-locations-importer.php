<?php
/**
 * Locations Data Importer
 *
 * Admin page to import location data from JavaScript to WordPress post types.
 * Only accessible to user 'admin-mbm'.
 *
 * @package Fire
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add import page under Tools menu
 */
function fire_locations_importer_menu() {
	// Only show menu for admin-mbm user
	$current_user = wp_get_current_user();
	if ( $current_user->user_login !== 'admin-mbm' ) {
		return;
	}

	add_management_page(
		'Import Locations',
		'Import Locations',
		'manage_options',
		'fire-import-locations',
		'fire_locations_importer_page'
	);
}
add_action( 'admin_menu', 'fire_locations_importer_menu' );

/**
 * Render the import page
 */
function fire_locations_importer_page() {
	// Security check
	$current_user = wp_get_current_user();
	if ( $current_user->user_login !== 'admin-mbm' ) {
		wp_die( 'You do not have permission to access this page.' );
	}

	// Handle form submission
	if ( isset( $_POST['fire_import_locations_nonce'] ) && wp_verify_nonce( $_POST['fire_import_locations_nonce'], 'fire_import_locations' ) ) {
		$result = fire_process_locations_import();

		if ( is_wp_error( $result ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
		} else {
			echo '<div class="notice notice-success"><p>Successfully imported ' . esc_html( $result ) . ' locations!</p></div>';
		}
	}

	// Get current location count
	$existing_count = wp_count_posts( 'location' );
	$published_count = $existing_count->publish ?? 0;

	?>
	<div class="wrap">
		<h1>Import Locations from JavaScript Data</h1>

		<div class="card">
			<h2>Current Status</h2>
			<p><strong>Existing Locations:</strong> <?php echo esc_html( $published_count ); ?></p>
			<p>This tool will import location data from the JavaScript file and create WordPress location posts.</p>
			<p><strong>Note:</strong> Duplicate locations (same title and address) will be skipped.</p>
		</div>

		<form method="post" action="">
			<?php wp_nonce_field( 'fire_import_locations', 'fire_import_locations_nonce' ); ?>

			<table class="form-table">
				<tr>
					<th scope="row">Import Action</th>
					<td>
						<button type="submit" class="button button-primary" onclick="return confirm('This will import all location data. Continue?');">
							Import Locations
						</button>
						<p class="description">Click to import all non-commented location entries from the data file.</p>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php
}

/**
 * Process the locations import
 *
 * @return int|WP_Error Number of imported locations or error
 */
function fire_process_locations_import() {
	// Get the locations data
	$locations_data = fire_get_import_locations_data();

	if ( empty( $locations_data ) ) {
		return new WP_Error( 'no_data', 'No location data found to import.' );
	}

	$imported = 0;
	$skipped = 0;

	foreach ( $locations_data as $location ) {
		// Check if location already exists (by title and address1)
		$existing = get_posts( array(
			'post_type' => 'location',
			'title' => $location['title'],
			'posts_per_page' => 1,
			'post_status' => 'any',
		) );

		if ( ! empty( $existing ) ) {
			// Check if address matches
			$existing_address = get_field( 'address_line_1', $existing[0]->ID );
			if ( $existing_address === $location['address1'] ) {
				$skipped++;
				continue;
			}
		}

		// Create the post
		$post_id = wp_insert_post( array(
			'post_title'  => $location['title'],
			'post_type'   => 'location',
			'post_status' => 'publish',
		) );

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		// Update ACF fields
		update_field( 'address_line_1', $location['address1'], $post_id );
		update_field( 'address_line_2', $location['address2'], $post_id );
		update_field( 'latitude', $location['coords']['lat'], $post_id );
		update_field( 'longitude', $location['coords']['lng'], $post_id );

		// Get website URL - it's stored directly in the data array
		if ( ! empty( $location['website'] ) ) {
			update_field( 'website_url', $location['website'], $post_id );
		}

		$imported++;
	}

	return $imported;
}

/**
 * Get locations data to import
 *
 * @return array Array of location data
 */
function fire_get_import_locations_data() {
	// Load the data file
	require_once get_template_directory() . '/inc/fire-locations-data.php';
	return fire_get_all_import_data();
}

