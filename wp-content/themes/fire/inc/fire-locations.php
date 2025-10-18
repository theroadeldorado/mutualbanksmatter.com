<?php
/**
 * Location Helper Functions
 *
 * Note: The Location post type is registered via ACF JSON in:
 * /acf-json/post_type_location.json
 *
 * @package Fire
 */

/**
 * Get all locations formatted for the map
 *
 * @return array Array of location data
 */
function fire_get_locations_for_map() {
	$locations = array();

	$args = array(
		'post_type'      => 'location',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			// Get ACF fields
			$address_line_1 = get_field( 'address_line_1', $post_id );
			$address_line_2 = get_field( 'address_line_2', $post_id );
			$latitude       = get_field( 'latitude', $post_id );
			$longitude      = get_field( 'longitude', $post_id );
			$website_url    = get_field( 'website_url', $post_id );

			// Only include locations with coordinates
			if ( $latitude && $longitude ) {
				$location = array(
					'title'    => get_the_title(),
					'address1' => $address_line_1 ? $address_line_1 : '',
					'address2' => $address_line_2 ? $address_line_2 : '',
					'coords'   => array(
						'lat' => (float) $latitude,
						'lng' => (float) $longitude,
					),
					'placeId'  => '', // Could add this as a field if needed
					'actions'  => array(),
				);

				// Add website action if URL exists
				if ( $website_url ) {
					$location['actions'][] = array(
						'label'      => 'Website',
						'defaultUrl' => $website_url,
					);
				}

				$locations[] = $location;
			}
		}
		wp_reset_postdata();
	}

	return $locations;
}

/**
 * Add custom columns to location admin list
 */
function fire_location_admin_columns( $columns ) {
	$new_columns = array();

	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;

		// Add address column after title
		if ( $key === 'title' ) {
			$new_columns['address'] = 'Address';
			$new_columns['coordinates'] = 'Coordinates';
		}
	}

	return $new_columns;
}
add_filter( 'manage_location_posts_columns', 'fire_location_admin_columns' );

/**
 * Populate custom columns with data
 */
function fire_location_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'address':
			$address_line_1 = get_field( 'address_line_1', $post_id );
			$address_line_2 = get_field( 'address_line_2', $post_id );
			$full_address = trim( $address_line_1 . ', ' . $address_line_2, ', ' );
			echo esc_html( $full_address );
			break;

		case 'coordinates':
			$latitude = get_field( 'latitude', $post_id );
			$longitude = get_field( 'longitude', $post_id );
			if ( $latitude && $longitude ) {
				echo esc_html( number_format( $latitude, 6 ) . ', ' . number_format( $longitude, 6 ) );
			} else {
				echo '<span style="color: #999;">Not set</span>';
			}
			break;
	}
}
add_action( 'manage_location_posts_custom_column', 'fire_location_admin_column_content', 10, 2 );

/**
 * Make custom columns sortable
 */
function fire_location_sortable_columns( $columns ) {
	$columns['address'] = 'address';
	return $columns;
}
add_filter( 'manage_edit-location_sortable_columns', 'fire_location_sortable_columns' );

/**
 * Enqueue admin scripts for location post type
 */
function fire_location_admin_scripts( $hook ) {
	global $post_type;

	// Only load on location post type edit screens
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'location' === $post_type ) {
		wp_enqueue_script(
			'fire-location-geocode',
			get_template_directory_uri() . '/inc/fire-location-geocode.js',
			array( 'jquery', 'acf-input' ),
			'1.0.0',
			true
		);

		wp_localize_script(
			'fire-location-geocode',
			'fireLocationGeocodeData',
			array(
				'nonce' => wp_create_nonce( 'fire_geocode_nonce' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'fire_location_admin_scripts' );

