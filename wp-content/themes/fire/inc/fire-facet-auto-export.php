<?php

if (function_exists( 'FWP' ) ) {
  /**
   * Export facets to JSON when facets are created or updated
   *
   * This code automatically exports FacetWP facets and templates to JSON files
   * whenever they are created, updated, or manually triggered. The export process
   * is hooked into WordPress option updates to ensure exports happen only when
   * facets are actually saved.
   */

  // Initialize facet export directory and security on theme load
  add_action('after_setup_theme', 'fire_init_facet_export_directory');

  function fire_init_facet_export_directory() {
      $facet_json_dir = get_template_directory() . '/facet-json';

      // Create directory if it doesn't exist
      if (!file_exists($facet_json_dir)) {
          wp_mkdir_p($facet_json_dir);
      }

      // Add .htaccess for security (prevent direct access)
      $htaccess_file = $facet_json_dir . '/.htaccess';
      if (!file_exists($htaccess_file)) {
          $htaccess_content = "# Prevent direct access to JSON files\n";
          $htaccess_content .= "Order deny,allow\n";
          $htaccess_content .= "Deny from all\n";
          $htaccess_content .= "<Files \"*.json\">\n";
          $htaccess_content .= "    Order deny,allow\n";
          $htaccess_content .= "    Deny from all\n";
          $htaccess_content .= "</Files>\n";

          file_put_contents($htaccess_file, $htaccess_content);
      }

      // Add index.php for additional security
      $index_file = $facet_json_dir . '/index.php';
      if (!file_exists($index_file)) {
          file_put_contents($index_file, "<?php\n// Silence is golden.\n");
      }
  }

  // Hook into WordPress option updates to catch FacetWP saves (this is the main trigger)
  add_action('update_option_facetwp_settings', 'fire_facetwp_settings_updated', 10, 3);

  function fire_facetwp_settings_updated($old_value, $value, $option) {
      if ( ! class_exists( 'FacetWP' ) ) {
          return;
      }
      // Only export if the settings actually changed
      if ($old_value !== $value) {
          wp_schedule_single_event(time() + 1, 'fire_delayed_facet_export');
      }
  }

  // Handle the delayed export
  add_action('fire_delayed_facet_export', 'fire_perform_facet_export');

  function fire_perform_facet_export() {
      if ( ! class_exists( 'FacetWP' ) ) {
          return false;
      }
      try {
          // Get FacetWP settings which contains facets and templates
          $facetwp_settings = get_option('facetwp_settings', '');
          $settings_data = json_decode($facetwp_settings, true);

          $facets = array();
          $templates = array();

          if (is_array($settings_data)) {
              $facets = isset($settings_data['facets']) ? $settings_data['facets'] : array();
              $templates = isset($settings_data['templates']) ? $settings_data['templates'] : array();
          }

          // Create the facet-json directory if it doesn't exist
          $facet_json_dir = get_template_directory() . '/facet-json';

          if (!file_exists($facet_json_dir)) {
              wp_mkdir_p($facet_json_dir);
          }

          // Generate timestamped filename
          $timestamp = current_time('Y-m-d_H-i-s');
          $filename = "facets_{$timestamp}.json";
          $file_path = $facet_json_dir . '/' . $filename;

          // Prepare facet data for export (match standard FacetWP export format)
          $export_data = array(
              'facets' => array_values($facets),
              'templates' => array_values($templates)
          );

          // Write JSON file
          $json_data = wp_json_encode($export_data, JSON_UNESCAPED_SLASHES);
          $result = file_put_contents($file_path, $json_data);

          if ($result === false) {
              return false;
          }

          // Clean up old files (keep only the latest 10)
          fire_cleanup_old_facet_exports($facet_json_dir);

          return true;

      } catch (Exception $e) {
          return false;
      }
  }

  // Cleanup function to keep only the latest facet export files
  function fire_cleanup_old_facet_exports($facet_json_dir, $keep_files = 10) {
      $files = glob($facet_json_dir . '/facets_*.json');

      if (count($files) > $keep_files) {
          // Sort files by modification time (newest first)
          array_multisort(array_map('filemtime', $files), SORT_DESC, $files);

          // Remove older files
          $files_to_remove = array_slice($files, $keep_files);
          foreach ($files_to_remove as $file) {
              unlink($file);
          }
      }
  }

  // AJAX handler for manual export
  add_action('wp_ajax_fire_export_facets_json', 'fire_export_facets_json_handler');

  function fire_export_facets_json_handler() {
      // Verify nonce
      if (!wp_verify_nonce($_POST['nonce'], 'fire_export_facets_nonce')) {
          wp_die('Security check failed');
      }

      // Check user permissions
      if (!current_user_can('manage_options')) {
          wp_die('Insufficient permissions');
      }

      $result = fire_perform_facet_export();

      if ($result) {
          $files = glob(get_template_directory() . '/facet-json/facets_*.json');
          $latest_file = '';
          if (!empty($files)) {
              array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
              $latest_file = basename($files[0]);
          }

          wp_send_json_success(array(
              'filename' => $latest_file,
              'message' => 'Facets exported successfully!'
          ));
      } else {
          wp_send_json_error('Export failed. Check error logs for details.');
      }
  }

  // Add manual export button to FacetWP admin page
  add_action('admin_init', 'fire_add_facet_export_button');

  function fire_add_facet_export_button() {
      // Only add on FacetWP pages
      if (isset($_GET['page']) && $_GET['page'] === 'facetwp') {
          add_action('admin_footer', 'fire_add_manual_export_button');
      }
  }

  function fire_add_manual_export_button() {
      ?>
      <script type="text/javascript">
      jQuery(document).ready(function($) {
          // Add manual export button to FacetWP admin
          if ($('.facetwp-region').length) {
              var exportSection = $('<div class="fire-facet-export-section" style="margin-bottom: 20px; padding: 15px; background: #f1f1f1; border-left: 4px solid #0073aa; border-radius: 4px;"></div>');
              exportSection.html('<h3 style="margin: 0 0 10px 0; font-size: 14px;">Facet JSON Export</h3><p style="margin: 0 0 10px 0; font-size: 12px; color: #666;">Automatically exports facets to JSON when saved. You can also export manually:</p><button type="button" id="fire-export-facets" class="button button-secondary">Export All Facets to JSON</button><span id="fire-export-status" style="margin-left: 10px; font-style: italic;"></span>');

              $('.facetwp-region').first().prepend(exportSection);

              $('#fire-export-facets').on('click', function() {
                  var $btn = $(this);
                  var $status = $('#fire-export-status');

                  $btn.prop('disabled', true).text('Exporting...');
                  $status.text('');

                  $.ajax({
                      url: ajaxurl,
                      type: 'POST',
                      data: {
                          action: 'fire_export_facets_json',
                          nonce: '<?php echo wp_create_nonce('fire_export_facets_nonce'); ?>'
                      },
                      success: function(response) {
                          if (response.success) {
                              $status.html('<span style="color: #0073aa;">✓ ' + response.data.message + ' File: ' + response.data.filename + '</span>');
                          } else {
                              $status.html('<span style="color: #d63638;">✗ ' + response.data + '</span>');
                          }
                          $btn.prop('disabled', false).text('Export All Facets to JSON');
                      },
                      error: function(xhr, status, error) {
                          $status.html('<span style="color: #d63638;">✗ AJAX error: ' + error + '</span>');
                          $btn.prop('disabled', false).text('Export All Facets to JSON');
                      }
                  });
              });
          }
      });
      </script>
      <style>
      #fire-export-facets:disabled {
          opacity: 0.6;
          cursor: not-allowed;
      }
      .fire-facet-export-section {
          border-radius: 4px;
      }
      </style>
      <?php
  }
}