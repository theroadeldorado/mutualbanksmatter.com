<?php

function fire_register_options_page() {
  if (function_exists('acf_add_options_page')) {
    /*
      * Creates options page for global settings
      */
    acf_add_options_page(array(
      'page_title' => 'Site Settings',
      'menu_title' => 'Site Settings',
      'position' => '2',
      'post_id' => 'site_settings',
      'capability' => 'edit_posts',
    ));
  }
}
add_action('acf/init', 'fire_register_options_page', 10);


function skycatchfire_set_default_admin_color($user_id) {
  $args = array(
    'ID' => $user_id,
    'admin_color' => 'skycatchfire'
  );
  wp_update_user($args);
}
add_action('user_register', 'skycatchfire_set_default_admin_color');


function update_flexible_content_acf_fields() {
?>
  <script type="text/javascript">
    (function($) {
      $(document).ready(function() {
        // add a event handler for when and acf acf-flexible-content layout is collapsed
        function updateLayoutHandle($layout) {
          var text = '';
          $layout.find('input[type="text"], textarea, .wp-editor-area').each(function() {
            text += $(this).val() + ' ';
          });
          text = text.replace(/(<([^>]+)>)/ig, '');
          text = text.substr(0, 65);
          if (text.length === 65) {
            text = text + '...';
          }
          if (text.replace(/\s/g, '').length) {
            var $handle = $layout.find('.acf-fc-layout-handle');
            var $existingTitle = $handle.find('.acf-fc-titles');
            if ($existingTitle.length) {
              $existingTitle.replaceWith('<span class="acf-fc-titles"> — ' + text + '</span>');
            } else {
              $handle.append('<span class="acf-fc-titles"> — ' + text + '</span>');
            }
          }
        }

        $(document).on('click', '.acf-flexible-content .layout.-collapsed', function() {
          var $layout = $(this);
          setTimeout(function() {
            updateLayoutHandle($layout);
            // if hidden append hiddenPill
            var isVisible = acf.getField($layout.find('.acf-field-true-false[data-name="is_visible"]'));
            var isVisibleEl = isVisible.$el;
            var inputField = isVisibleEl.find(`input[type="checkbox"]`);
            if (!inputField.is(':checked') && !$layout.find('.acf-fc-layout-handle').find('.section--invisible-btn').length) {
              $layout.find('.acf-fc-layout-handle').append('<div class="section--invisible-btn">Hidden</div>');
            }
          }, 600);
        });

        $('.acf-field-flexible-content .acf-input .layout').each(function() {
          updateLayoutHandle($(this));
        });

        // Create the button
        var $button = $('<button type="button" class="fire-collapse-all" alt="Collapse All Sections"><svg viewBox="0 0 512 448" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M464 384C464 392.8 456.8 400 448 400H64C55.2 400 48 392.8 48 384V320C48 311.2 55.2 304 64 304H448C456.8 304 464 311.2 464 320V384ZM512 320C512 284.7 483.3 256 448 256H280V169.9L303 192.9C312.4 202.3 327.6 202.3 336.9 192.9C346.2 183.5 346.3 168.3 336.9 159L272.9 95C263.5 85.6 248.3 85.6 239 95L175 159C165.6 168.4 165.6 183.6 175 192.9C184.4 202.2 199.6 202.3 208.9 192.9L231.9 169.9V256H64C28.7 256 0 284.7 0 320V384C0 419.3 28.7 448 64 448H448C483.3 448 512 419.3 512 384V320ZM64 192H138.3C132.6 172.9 137.3 151.5 152.4 136.4L216.4 72.4C238.3 50.5 273.7 50.5 295.6 72.4L359.6 136.4C374.6 151.4 379.3 172.9 373.7 192H448C483.3 192 512 163.3 512 128V64C512 28.7 483.3 0 448 0H64C28.7 0 0 28.7 0 64V128C0 163.3 28.7 192 64 192Z" fill="currentColor"/></svg></button>');

        // Prepend the button to the handle-actions of the acf-group_5eb2da1550e86
        $('#acf-group_5eb2da1550e86 .handle-actions').prepend($button);

        // Add click event to the button
        $button.click(function(event) {
          event.preventDefault();
          $('.acf-flexible-content .layout').each(function() {
            if (!$(this).hasClass('acf-clone')) {
              $(this).addClass('-collapsed');
            }
          });
        });

        // Add a button to toggle the visibility of the sections
        $('.acf-field-flexible-content .acf-input .layout:not(.acf-clone)').each(function() {
          var $layout = $(this);
          var $button = $(`<button type="button" class="fire-toggle-layout acf-icon small" aria-label="Toggle Section Visibility"><div class="fire-toggle-layout--visible"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1 3.3 7.9 3.3 16.7 0 24.6-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 400a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" class="fa-secondary" opacity=".4"/><path d="M224 256c35.3 0 64-28.7 64-64 0-7.1-1.2-13.9-3.3-20.3-1.8-5.5 1.6-11.9 7.4-11.7 40.8 1.7 77.5 29.6 88.6 71.1 13.7 51.2-16.7 103.9-67.9 117.6S208.9 332 195.2 280.8c-1.9-6.9-2.9-13.9-3.2-20.7-.3-5.8 6.1-9.2 11.7-7.4 6.4 2.1 13.2 3.3 20.3 3.3z" class="fa-primary"/></svg></div><div class="fire-toggle-layout--invisible"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="none"><path stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="20" d="M20 252c187.227 162.667 321.094 162.667 535 0M68 347l-30 64M161 391l-20 64M287 409.5v64M412.5 388.5l23.5 64M509 342l30 64"/></svg></div></button>`);
          $layout.find('.acf-fc-layout-controls').first().prepend($button);

          var hiddenPill = `<div class="section--invisible-btn">Hidden</div>`;
          setTimeout(function() {
            var isVisible = acf.getField($layout.find('.acf-field-true-false[data-name="is_visible"]'));
            var isVisibleEl = isVisible.$el;
            var inputField = isVisibleEl.find(`input[type="checkbox"]`);
            if (inputField.is(':checked')) {
              $layout.find('.fire-toggle-layout--visible').css('opacity', '1');
              $layout.find('.fire-toggle-layout--invisible').css('opacity', '0');
              isVisibleEl.find('.acf-switch').addClass('-on');
              $layout.find('.section--invisible-btn').remove();
            } else {
              $layout.find('.fire-toggle-layout--visible').css('opacity', '0');
              $layout.find('.fire-toggle-layout--invisible').css('opacity', '1');
              isVisibleEl.find('.acf-switch').removeClass('-on');
              $layout.addClass('section--invisible');
              $layout.find('.acf-fc-layout-handle').append(hiddenPill);
            }
          }, 600);
          $button.on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var isVisible = acf.getField($layout.find('.acf-field-true-false[data-name="is_visible"]'));
            var isVisibleEl = isVisible.$el;
            var inputField = isVisibleEl.find(`input[type="checkbox"]`);

            if (inputField.is(':checked')) {
              inputField.prop('checked', false);
              $layout.find('.fire-toggle-layout--visible').css('opacity', '0');
              $layout.find('.fire-toggle-layout--invisible').css('opacity', '1');
              isVisibleEl.find('.acf-switch').removeClass('-on');
              $layout.addClass('section--invisible');
              $layout.find('.acf-fc-layout-handle').append(hiddenPill);
            } else {
              inputField.prop('checked', true);
              $layout.find('.fire-toggle-layout--visible').css('opacity', '1');
              $layout.find('.fire-toggle-layout--invisible').css('opacity', '0');
              isVisibleEl.find('.acf-switch').addClass('-on');
              $layout.removeClass('section--invisible');
              $layout.find('.section--invisible-btn').remove();
            }
          });
        });
      });
    })(jQuery);
  </script>
<?php
}
add_action('admin_footer', 'update_flexible_content_acf_fields');

// Only add the filter if Yoast SEO is active
if (function_exists('wpseo_init')) {
  add_filter('wpseo_opengraph_title', function($title) {
    // Don't run if Yoast Premium is active
    if (defined('WPSEO_PREMIUM_FILE')) {
      return $title;
    }

    $yoast_title = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true);
    if (!empty($yoast_title)) {
      return wpseo_replace_vars($yoast_title, get_post(get_the_ID()));
    }

    return $title;
  });
}
?>