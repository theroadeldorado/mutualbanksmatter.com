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
	$submitted_json = '';
	if ( isset( $_POST['fire_import_locations_nonce'] ) && wp_verify_nonce( $_POST['fire_import_locations_nonce'], 'fire_import_locations' ) ) {
		$dry_run = isset( $_POST['dry_run'] );
		// Use wp_unslash to remove WordPress magic quotes
		$json_data = isset( $_POST['json_locations'] ) ? trim( wp_unslash( $_POST['json_locations'] ) ) : '';

		// Preserve the submitted JSON for display
		$submitted_json = $json_data;

		if ( ! empty( $json_data ) ) {
			$result = fire_process_json_locations_import( $json_data, $dry_run );
		} else {
			$result = fire_process_locations_import( $dry_run );
		}

		if ( is_wp_error( $result ) ) {
		// Use wp_kses to allow basic HTML formatting in error messages
		$allowed_html = array(
			'br' => array(),
			'strong' => array(),
			'div' => array(
				'style' => array(),
			),
			'h3' => array(
				'style' => array(),
			),
			'h4' => array(
				'style' => array(),
			),
			'p' => array(
				'style' => array(),
			),
			'code' => array(
				'style' => array(),
			),
		);
		echo '<div class="notice notice-error"><p><strong>Import Error:</strong><br>' . wp_kses( $result->get_error_message(), $allowed_html ) . '</p></div>';
	} else {
		$mode = $dry_run ? 'Dry Run' : 'Import';
		$total_locations = $result['imported'] + $result['skipped'];

		echo '<div class="notice notice-success">';

		// Summary box
		echo '<div style="background: #f0f6fc; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px;">';
		echo '<h3 style="margin: 0 0 10px 0;">' . esc_html( $mode ) . ' Summary</h3>';
		echo '<p style="margin: 5px 0;"><strong>Total Locations Processed:</strong> ' . esc_html( $total_locations ) . '</p>';
		echo '<p style="margin: 5px 0; color: #2271b1;"><strong>New Locations:</strong> ' . esc_html( $result['imported'] ) . '</p>';
		echo '<p style="margin: 5px 0; color: #d63638;"><strong>Duplicate Locations:</strong> ' . esc_html( $result['skipped'] ) . '</p>';
		if ( ! empty( $result['errors'] ) ) {
			echo '<p style="margin: 5px 0; color: #d63638;"><strong>Errors:</strong> ' . esc_html( count( $result['errors'] ) ) . '</p>';
		}
		echo '</div>';

		// Show errors if any
		if ( ! empty( $result['errors'] ) ) {
			echo '<div style="max-height: 200px; overflow: auto; border: 1px solid #ddd; padding: 15px; background: #fff; margin-bottom: 15px;">';
			echo '<h4 style="margin-top: 0;">Errors:</h4>';
			echo '<ul style="margin-left: 20px;">';
			foreach ( $result['errors'] as $error ) {
				echo '<li>' . esc_html( $error ) . '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}

		// Show added locations in scrollable container if any
		if ( ! empty( $result['added'] ) ) {
			echo '<div style="max-height: 300px; overflow: auto; border: 1px solid #ddd; padding: 15px; background: #fff;">';
			echo '<h4 style="margin-top: 0;">Locations Added (' . count( $result['added'] ) . '):</h4>';
			echo '<ul style="margin-left: 20px;">';
			foreach ( $result['added'] as $location ) {
				echo '<li>' . esc_html( $location ) . '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}

		if ( $dry_run ) {
			echo '<p style="margin-top: 15px;"><em>This was a dry run. No locations were actually created.</em></p>';
		} else {
			// Clear the textarea on successful import
			$submitted_json = '';
		}
		echo '</div>';
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

			<h2>Option 1: Import from Data File</h2>
			<table class="form-table">
				<tr>
					<th scope="row">Import Action</th>
					<td>
						<button type="submit" name="dry_run" value="1" class="button">
							Dry Run (Preview)
						</button>
						<button type="submit" class="button button-primary" onclick="return confirm('This will import all location data. Continue?');">
							Import Locations
						</button>
						<p class="description">Import all non-commented location entries from the data file.</p>
					</td>
				</tr>
			</table>
		</form>

		<hr style="margin: 30px 0;">

		<form method="post" action="">
			<?php wp_nonce_field( 'fire_import_locations', 'fire_import_locations_nonce' ); ?>

			<h2>Option 2: Import from JSON</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="json_locations">JSON Data</label>
					</th>
					<td>
						<textarea
							name="json_locations"
							id="json_locations"
							rows="15"
							class="large-text code"
							placeholder='Paste your JSON array here, e.g.:
[
  {
    "title": "Dean Bank",
    "address1": "32 Hastings St.",
    "address2": "Mendon, MA  01756",
    "coords": { "lat": 42.1035112, "lng": -71.5583633 },
    "placeId": "ChIJZ4cTzDpt5IkRAanMNfA9s7I",
    "actions": [
      {
        "label": "Website",
        "defaultUrl": "https://www.deanbank.com/"
      }
    ]
  }
]'
						><?php echo esc_textarea( $submitted_json ); ?></textarea>
						<p class="description">
							Paste a JSON array of locations. Each location should have: title, address1, address2, coords (lat/lng).<br>
							Optional fields: placeId, actions (array with label and defaultUrl), website.
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Import Action</th>
					<td>
						<button type="submit" name="dry_run" value="1" class="button">
							Dry Run (Preview)
						</button>
						<button type="submit" class="button button-primary" onclick="return confirm('This will import the JSON locations. Continue?');">
							Import JSON Locations
						</button>
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
 * @param bool $dry_run Whether to perform a dry run without creating posts
 * @return array|WP_Error Import results or error
 */
function fire_process_locations_import( $dry_run = false ) {
	// Get the locations data
	$locations_data = fire_get_import_locations_data();

	if ( empty( $locations_data ) ) {
		return new WP_Error( 'no_data', 'No location data found to import.' );
	}

	return fire_import_locations_array( $locations_data, $dry_run );
}

/**
 * Process JSON locations import
 *
 * @param string $json_data JSON string of locations
 * @param bool $dry_run Whether to perform a dry run without creating posts
 * @return array|WP_Error Import results or error
 */
function fire_process_json_locations_import( $json_data, $dry_run = false ) {
	// Clean up the JSON data to handle JavaScript object notation

	// Remove trailing commas before closing brackets/braces
	$json_data = preg_replace( '/,\s*([}\]])/', '$1', $json_data );

	// Convert unquoted keys to quoted keys (JavaScript object notation to JSON)
	$json_data = preg_replace( '/([{,]\s*)([a-zA-Z_][a-zA-Z0-9_]*)\s*:/', '$1"$2":', $json_data );

	// Validate JSON structure before parsing
	$validation_errors = fire_validate_json_structure( $json_data );

	if ( ! empty( $validation_errors ) ) {
		// All validation errors are real issues that need fixing
		$total_locations = fire_count_locations_in_json( $json_data );
		$total_issues = count( $validation_errors );

		$summary = '<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #d63638; margin-bottom: 20px;">';
		$summary .= '<h3 style="margin: 0 0 10px 0;">Validation Results</h3>';
		$summary .= '<p style="margin: 5px 0;"><strong>Total Locations in JSON:</strong> ' . $total_locations . '</p>';
		$summary .= '<p style="margin: 5px 0; color: #d63638;"><strong>Issues Found:</strong> ' . $total_issues . '</p>';
		$summary .= '<p style="margin: 10px 0 0 0; font-style: italic;">Fix the issues below, then try again.</p>';
		$summary .= '</div>';

		// Show all issues that need to be fixed
		$error_details = '<div style="max-height: 400px; overflow: auto; border: 1px solid #ddd; padding: 15px; background: #fff;">';
		$error_details .= '<h4 style="margin-top: 0;">Issues That Need Fixing:</h4>';

		foreach ( $validation_errors as $error ) {
			$error_details .= '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">• ' . $error . '</div>';
		}

		$error_details .= '</div>';

		$error_message = $summary . $error_details . '<br><p><strong>Please fix these issues and try again.</strong></p>';

		return new WP_Error( 'validation_failed', $error_message );
	}

	// Decode JSON
	$locations_data = json_decode( $json_data, true );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		$error_msg = json_last_error_msg();

		// Run validation again to get specific issues
		$late_validation = fire_validate_json_structure( $json_data );

		if ( ! empty( $late_validation ) ) {
			$error_message = 'JSON parsing failed. Found ' . count( $late_validation ) . ' issue(s):<br><br>';
			foreach ( $late_validation as $error ) {
				$error_message .= '• ' . $error . '<br>';
			}
			return new WP_Error( 'json_parse_failed', $error_message );
		}

		// Try to find location names for context
		preg_match_all( '/"title"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $json_data, $title_matches );
		$location_titles = array();
		if ( ! empty( $title_matches[1] ) ) {
			foreach ( $title_matches[1] as $title ) {
				$location_titles[] = stripcslashes( $title );
			}
		}

		$line_hint = '';
		if ( count( $location_titles ) > 0 ) {
			$line_hint = '<br><br>Locations found before error: ' . count( $location_titles ) . '<br>Last location: <strong>' . esc_html( end( $location_titles ) ) . '</strong><br>The error is likely in or after this location.';
		}

		return new WP_Error(
			'invalid_json',
			$error_msg . $line_hint
		);
	}

	if ( empty( $locations_data ) || ! is_array( $locations_data ) ) {
		return new WP_Error( 'no_data', 'No location data found in JSON.' );
	}

	return fire_import_locations_array( $locations_data, $dry_run );
}

/**
 * Count total locations in JSON data
 *
 * @param string $json_data The JSON string
 * @return int Number of locations found
 */
function fire_count_locations_in_json( $json_data ) {
	// Quick count by counting occurrences of "title" field at location level
	preg_match_all( '/"title"\s*:\s*"/', $json_data, $matches );
	return count( $matches[0] );
}

/**
 * Validate JSON structure and find common issues
 *
 * @param string $json_data The JSON string to validate
 * @return array Array of error messages
 */
function fire_validate_json_structure( $json_data ) {
	$errors = array();

	// Split JSON into location objects using a better approach
	// Find all occurrences of title fields to identify locations
	$location_pattern = '/\{[^{]*?"title"\s*:\s*"([^"]+)".*?\}/s';

	// Use a more robust approach: parse to find each top-level object with a title field
	$locations = array();
	$in_string = false;
	$escape_next = false;
	$depth = 0;
	$current_location = '';
	$capturing = false;
	$array_depth = 0; // Track array depth separately

	$length = strlen( $json_data );
	for ( $i = 0; $i < $length; $i++ ) {
		$char = $json_data[$i];

		// Handle string escaping
		if ( $escape_next ) {
			if ( $capturing ) {
				$current_location .= $char;
			}
			$escape_next = false;
			continue;
		}

		if ( $char === '\\' ) {
			$escape_next = true;
			if ( $capturing ) {
				$current_location .= $char;
			}
			continue;
		}

		// Track if we're inside a string
		if ( $char === '"' ) {
			$in_string = ! $in_string;
			if ( $capturing ) {
				$current_location .= $char;
			}
			continue;
		}

		// Only track braces/brackets outside of strings
		if ( ! $in_string ) {
			if ( $char === '[' ) {
				$array_depth++;
				if ( $capturing ) {
					$current_location .= $char;
				}
			} elseif ( $char === ']' ) {
				$array_depth--;
				if ( $capturing ) {
					$current_location .= $char;
				}
			} elseif ( $char === '{' ) {
				$depth++;
				// Start capturing if we're inside the main array (array_depth === 1) and entering an object
				if ( $array_depth === 1 && $depth === 1 ) {
					$capturing = true;
					$current_location = $char;
				} elseif ( $capturing ) {
					$current_location .= $char;
				}
			} elseif ( $char === '}' ) {
				if ( $capturing ) {
					$current_location .= $char;
				}

				// If we're closing a top-level object (array_depth === 1, depth === 1)
				if ( $array_depth === 1 && $depth === 1 && $capturing ) {
					// Check if it has a title field
					if ( strpos( $current_location, '"title"' ) !== false || strpos( $current_location, 'title:' ) !== false ) {
						$locations[] = $current_location;
					}
					$current_location = '';
					$capturing = false;
				}
				$depth--;
			} elseif ( $capturing ) {
				$current_location .= $char;
			}
		} elseif ( $capturing ) {
			$current_location .= $char;
		}
	}

	// Debug info to show how many locations were found
	$location_count = count( $locations );

	// If no locations found, provide detailed debugging
	if ( empty( $locations ) ) {
		$trimmed = trim( $json_data );
		$first_chars = substr( $trimmed, 0, 100 );
		$starts_with_bracket = ( strpos( $trimmed, '[' ) === 0 );
		$has_opening_brace = ( strpos( $trimmed, '{' ) !== false );
		$brace_count_open = substr_count( $json_data, '{' );
		$brace_count_close = substr_count( $json_data, '}' );
		$bracket_count_open = substr_count( $json_data, '[' );
		$bracket_count_close = substr_count( $json_data, ']' );
		$has_title = ( strpos( $json_data, '"title"' ) !== false || strpos( $json_data, 'title:' ) !== false );

		$debug_info = '<br><br><strong>Debug Information:</strong><br>';
		$debug_info .= '• Locations parsed: <strong>' . $location_count . '</strong><br>';
		$debug_info .= '• Data length: ' . strlen( $json_data ) . ' characters<br>';
		$debug_info .= '• Starts with [ ? ' . ( $starts_with_bracket ? '<strong>Yes ✓</strong>' : '<strong style="color: red;">No ✗</strong> (JSON should start with [)' ) . '<br>';
		$debug_info .= '• Has opening braces { ? ' . ( $has_opening_brace ? 'Yes ✓' : '<strong style="color: red;">No ✗</strong>' ) . '<br>';
		$debug_info .= '• Opening braces {: ' . $brace_count_open . ', Closing braces }: ' . $brace_count_close;
		if ( $brace_count_open !== $brace_count_close ) {
			$debug_info .= ' <strong style="color: red;">✗ MISMATCHED!</strong> (missing ' . abs( $brace_count_open - $brace_count_close ) . ')';
		} else {
			$debug_info .= ' ✓';
		}
		$debug_info .= '<br>• Opening brackets [: ' . $bracket_count_open . ', Closing brackets ]: ' . $bracket_count_close;
		if ( $bracket_count_open !== $bracket_count_close ) {
			$debug_info .= ' <strong style="color: red;">✗ MISMATCHED!</strong> (missing ' . abs( $bracket_count_open - $bracket_count_close ) . ')';
		} else {
			$debug_info .= ' ✓';
		}
		$debug_info .= '<br>• Has "title" field? ' . ( $has_title ? 'Yes ✓' : '<strong style="color: red;">No ✗</strong>' ) . '<br>';
		$debug_info .= '<br><strong>First 100 characters:</strong><br><code style="display: block; background: #f5f5f5; padding: 10px; margin: 5px 0; white-space: pre-wrap;">' . esc_html( $first_chars ) . '</code>';

		return array( 'Unable to parse location objects. Make sure your JSON is formatted as an array of location objects.' . $debug_info );
	}

	// Check each location for issues
	foreach ( $locations as $index => $location_json ) {
		// Extract title and placeId for error reporting
		preg_match( '/"title"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $location_json, $title_match );
		preg_match( '/"placeId"\s*:\s*"([^"]+)"/', $location_json, $place_match );

		$title = isset( $title_match[1] ) ? $title_match[1] : 'Unknown Location #' . ( $index + 1 );
		// Decode escaped characters in title for display
		$title = stripcslashes( $title );
		$place_id = $place_match[1] ?? '';

		$location_identifier = $title;
		if ( $place_id ) {
			$location_identifier .= ' (PlaceID: ' . substr( $place_id, 0, 20 ) . '...)';
		}

		// Check for duplicate "actions" fields
		$actions_count = substr_count( $location_json, '"actions"' );
		if ( $actions_count > 1 ) {
			$errors[] = '<strong>' . esc_html( $location_identifier ) . '</strong> has <strong>duplicate "actions" fields</strong> (' . $actions_count . ' found). Combine them into one "actions" array.';
		}

		// Check for duplicate other common fields
		$duplicate_fields = array( 'title', 'address1', 'address2', 'coords' );
		foreach ( $duplicate_fields as $field ) {
			$field_count = substr_count( $location_json, '"' . $field . '"' );
			if ( $field_count > 1 ) {
				$errors[] = '<strong>' . esc_html( $location_identifier ) . '</strong> has <strong>duplicate "' . $field . '" fields</strong> (' . $field_count . ' found).';
			}
		}

		// Check for common missing comma issues (closing brace/bracket followed by quote without comma)
		if ( preg_match( '/[}\]]\s*"/', $location_json ) ) {
			$errors[] = '<strong>' . esc_html( $location_identifier ) . '</strong> may be <strong>missing a comma</strong> between properties or array elements.';
		}
	}

	// Check for duplicate locations (within JSON and against existing WordPress locations)
	$duplicate_errors = fire_check_duplicate_locations( $locations );
	$errors = array_merge( $errors, $duplicate_errors );

	return $errors;
}

/**
 * Check for duplicate locations based on placeId, address, and coordinates
 *
 * @param array $locations Array of location JSON strings
 * @return array Array of error messages for duplicates found
 */
function fire_check_duplicate_locations( $locations ) {
	$errors = array();
	$parsed_locations = array();

	// Parse all locations from JSON first
	foreach ( $locations as $index => $location_json ) {
		preg_match( '/"title"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $location_json, $title_match );
		preg_match( '/"placeId"\s*:\s*"([^"]+)"/', $location_json, $place_match );
		preg_match( '/"address1"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $location_json, $address1_match );
		preg_match( '/"address2"\s*:\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $location_json, $address2_match );
		preg_match( '/"lat"\s*:\s*([0-9.\-]+)/', $location_json, $lat_match );
		preg_match( '/"lng"\s*:\s*([0-9.\-]+)/', $location_json, $lng_match );

		$parsed_locations[] = array(
			'index' => $index,
			'title' => isset( $title_match[1] ) ? stripcslashes( $title_match[1] ) : '',
			'placeId' => $place_match[1] ?? '',
			'address1' => isset( $address1_match[1] ) ? stripcslashes( $address1_match[1] ) : '',
			'address2' => isset( $address2_match[1] ) ? stripcslashes( $address2_match[1] ) : '',
			'lat' => isset( $lat_match[1] ) ? floatval( $lat_match[1] ) : null,
			'lng' => isset( $lng_match[1] ) ? floatval( $lng_match[1] ) : null,
		);
	}

	// Get all existing WordPress locations
	$existing_locations = get_posts( array(
		'post_type' => 'location',
		'posts_per_page' => -1,
		'post_status' => 'any',
	) );

	$existing_parsed = array();
	foreach ( $existing_locations as $existing_post ) {
		$place_id = get_field( 'place_id', $existing_post->ID );
		$address1 = get_field( 'address_line_1', $existing_post->ID );
		$address2 = get_field( 'address_line_2', $existing_post->ID );
		$coords = get_field( 'coords', $existing_post->ID );

		$existing_parsed[] = array(
			'title' => $existing_post->post_title,
			'placeId' => $place_id ?? '',
			'address1' => $address1 ?? '',
			'address2' => $address2 ?? '',
			'lat' => isset( $coords['lat'] ) ? floatval( $coords['lat'] ) : null,
			'lng' => isset( $coords['lng'] ) ? floatval( $coords['lng'] ) : null,
			'source' => 'existing',
		);
	}

	// Note: We don't report duplicates as errors anymore
	// The import process will handle them by importing the first occurrence and skipping duplicates
	// Duplicates (whether in WP or in JSON) are handled gracefully during import

	return $errors;
}

/**
 * Check if two locations are duplicates
 *
 * @param array $loc1 First location
 * @param array $loc2 Second location
 * @return array ['duplicate' => bool, 'reason' => string]
 */
function fire_is_duplicate_location( $loc1, $loc2 ) {
	$is_duplicate = false;
	$reason = '';

	// Check 1: Same placeId (if both have one)
	if ( ! empty( $loc1['placeId'] ) && ! empty( $loc2['placeId'] ) ) {
		if ( $loc1['placeId'] === $loc2['placeId'] ) {
			return array(
				'duplicate' => true,
				'reason' => 'same Google Place ID'
			);
		} else {
			// Different PlaceIDs means Google considers them different locations
			// Skip all other checks - trust Google's data
			return array(
				'duplicate' => false,
				'reason' => ''
			);
		}
	}

	// Only check address/coords if at least one location is missing a PlaceID
	if ( empty( $loc1['placeId'] ) || empty( $loc2['placeId'] ) ) {
		// Check 2: Check address1
		if ( ! empty( $loc1['address1'] ) && ! empty( $loc2['address1'] ) ) {
			if ( strtolower( trim( $loc1['address1'] ) ) === strtolower( trim( $loc2['address1'] ) ) ) {
				// Address1 matches, check address2
				if ( ! empty( $loc1['address2'] ) && ! empty( $loc2['address2'] ) ) {
					if ( strtolower( trim( $loc1['address2'] ) ) === strtolower( trim( $loc2['address2'] ) ) ) {
						return array(
							'duplicate' => true,
							'reason' => 'same address (address1 + address2)'
						);
					}
				}
			}
		}

		// Check 3: Check coordinates if they're VERY close (< 0.1 miles ~500 feet)
		if ( $loc1['lat'] !== null && $loc1['lng'] !== null && $loc2['lat'] !== null && $loc2['lng'] !== null ) {
			$distance = fire_calculate_distance( $loc1['lat'], $loc1['lng'], $loc2['lat'], $loc2['lng'] );
			// Only flag as duplicate if within 0.1 miles (528 feet) - essentially the same building
			if ( $distance < 0.1 ) {
				return array(
					'duplicate' => true,
					'reason' => sprintf( 'coordinates within %.2f miles (same location)', $distance )
				);
			}
		}
	}

	return array(
		'duplicate' => false,
		'reason' => ''
	);
}

/**
 * Calculate distance between two lat/lng coordinates in miles using Haversine formula
 *
 * @param float $lat1 Latitude of first point
 * @param float $lng1 Longitude of first point
 * @param float $lat2 Latitude of second point
 * @param float $lng2 Longitude of second point
 * @return float Distance in miles
 */
function fire_calculate_distance( $lat1, $lng1, $lat2, $lng2 ) {
	$earth_radius_miles = 3959;

	$lat1_rad = deg2rad( $lat1 );
	$lat2_rad = deg2rad( $lat2 );
	$delta_lat = deg2rad( $lat2 - $lat1 );
	$delta_lng = deg2rad( $lng2 - $lng1 );

	$a = sin( $delta_lat / 2 ) * sin( $delta_lat / 2 ) +
		cos( $lat1_rad ) * cos( $lat2_rad ) *
		sin( $delta_lng / 2 ) * sin( $delta_lng / 2 );

	$c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );

	return $earth_radius_miles * $c;
}

/**
 * Import locations from an array
 *
 * @param array $locations_data Array of location data
 * @param bool $dry_run Whether to perform a dry run without creating posts
 * @return array Import results with counts and details
 */
function fire_import_locations_array( $locations_data, $dry_run = false ) {
	$imported = 0;
	$skipped = 0;
	$errors = array();
	$added = array();
	$imported_place_ids = array(); // Track PlaceIDs we've already imported in this batch

	foreach ( $locations_data as $index => $location ) {
		// Validate required fields
		if ( empty( $location['title'] ) ) {
			$errors[] = "Location #" . ( $index + 1 ) . ": Missing title";
			continue;
		}

		if ( empty( $location['coords']['lat'] ) || empty( $location['coords']['lng'] ) ) {
			$errors[] = $location['title'] . ": Missing or invalid coordinates";
			continue;
		}

		// Check if location already exists using PlaceID (most reliable), then address, then coordinates
		$is_duplicate = false;

		// First, check if we've already imported this PlaceID in this batch
		if ( ! empty( $location['placeId'] ) && in_array( $location['placeId'], $imported_place_ids ) ) {
			$is_duplicate = true;
		}

		// If location has a PlaceID, check if it exists in WordPress
		if ( ! $is_duplicate && ! empty( $location['placeId'] ) ) {
			$existing_by_place_id = get_posts( array(
				'post_type' => 'location',
				'posts_per_page' => 1,
				'post_status' => 'any',
				'meta_query' => array(
					array(
						'key' => 'place_id',
						'value' => $location['placeId'],
						'compare' => '='
					)
				)
			) );

			if ( ! empty( $existing_by_place_id ) ) {
				$is_duplicate = true;
			}
		}

		// If no PlaceID match, check by title + address
		if ( ! $is_duplicate ) {
			$existing = get_posts( array(
				'post_type' => 'location',
				'title' => $location['title'],
				'posts_per_page' => -1,
				'post_status' => 'any',
			) );

			foreach ( $existing as $existing_post ) {
				$existing_address = get_field( 'address_line_1', $existing_post->ID );
				$existing_address2 = get_field( 'address_line_2', $existing_post->ID );

				if ( $existing_address === ( $location['address1'] ?? '' ) &&
				     $existing_address2 === ( $location['address2'] ?? '' ) ) {
					$is_duplicate = true;
					break;
				}
			}
		}

		if ( $is_duplicate ) {
			$skipped++;
			continue;
		}

		// Record what would be added
		$location_name = $location['title'];
		if ( ! empty( $location['address1'] ) ) {
			$location_name .= ' - ' . $location['address1'];
		}
		$added[] = $location_name;

		// Track this PlaceID so we don't import duplicates in this batch
		if ( ! empty( $location['placeId'] ) ) {
			$imported_place_ids[] = $location['placeId'];
		}

		// If dry run, skip actual creation
		if ( $dry_run ) {
			$imported++;
			continue;
		}

		// Create the post
		$post_id = wp_insert_post( array(
			'post_title'  => $location['title'],
			'post_type'   => 'location',
			'post_status' => 'publish',
		) );

		if ( is_wp_error( $post_id ) ) {
			$errors[] = $location['title'] . ': Failed to create post - ' . $post_id->get_error_message();
			continue;
		}

		// Update ACF fields
		if ( ! empty( $location['address1'] ) ) {
			update_field( 'address_line_1', $location['address1'], $post_id );
		}
		if ( ! empty( $location['address2'] ) ) {
			update_field( 'address_line_2', $location['address2'], $post_id );
		}
		update_field( 'latitude', $location['coords']['lat'], $post_id );
		update_field( 'longitude', $location['coords']['lng'], $post_id );

		// Get website URL from actions or direct field
		if ( ! empty( $location['website'] ) ) {
			update_field( 'website_url', $location['website'], $post_id );
		} elseif ( ! empty( $location['actions'] ) && is_array( $location['actions'] ) ) {
			// Find website action
			foreach ( $location['actions'] as $action ) {
				if ( isset( $action['label'] ) && strtolower( $action['label'] ) === 'website' ) {
					if ( ! empty( $action['defaultUrl'] ) ) {
						update_field( 'website_url', $action['defaultUrl'], $post_id );
					}
					break;
				}
			}
		}

		$imported++;
	}

	return array(
		'imported' => $imported,
		'skipped' => $skipped,
		'errors' => $errors,
		'added' => $added,
	);
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

