<?php
/* Hover Module Effect */
function fire_acf_admin_head() {
  $module_images_path = get_template_directory_uri();
?>
  <style type="text/css">
    .imagePreview {
      position: absolute;
      right: 100%;
      top: 0px;
      z-index: 999999;
      border: 1px solid #f2f2f2;
      box-shadow: 0px 0px 9px #b6b6b6;
      background-color: #004f6d;
      padding: 5px;
    }

    .imagePreview img {
      width: 300px;
      height: auto;
      display: block;
    }

    .acf-tooltip li:hover {
      background-color: #0074a9;
    }
  </style>

  <script>
    jQuery(document).ready(function($) {
      function checkImage(urlToFile) {
        var xhr = new XMLHttpRequest();
        xhr.open('HEAD', urlToFile, false);
        xhr.send();

        if (xhr.status == "404") {
          return false;
        } else {
          return true;
        }
      }

      $("a[data-name=add-layout]").click(function() {
        waitForEl(".acf-tooltip li", function() {
          $(".acf-tooltip li a").hover(function() {
            var hoveredItem = $(this).closest('li');
            var parentUl = hoveredItem.closest('ul');
            var relativeTopPosition = hoveredItem.offset().top - parentUl.offset().top;
            var hoveredItemBottom = relativeTopPosition + hoveredItem.outerHeight();
            var ulHalfHeight = parentUl.height() / 2;

            imageTP = $(this).attr("data-layout");
            imageFullPath = "<?php echo $module_images_path; ?>/theme/assets/media/thumbnails/" + imageTP + '.jpg';
            if (checkImage(imageFullPath)) {
              $(".acf-tooltip").append('<div class="imagePreview"><img src="' + imageFullPath + '" /></div>');
              if (hoveredItemBottom > ulHalfHeight) {
                // Align the bottom of imagePreview with the bottom of the hovered item
                $(".imagePreview").css({
                  'top': 'auto',
                  'bottom': parentUl.outerHeight() - hoveredItemBottom + 'px'
                });
              } else {
                // Align the top of imagePreview with the top of the hovered item
                $(".imagePreview").css({
                  'top': relativeTopPosition + 'px',
                  'bottom': 'auto'
                });
              }
            } else {
              // if .imagePreview already exists, remove it
              if ($(".imagePreview").length) {
                $(".imagePreview").remove();
              }
            }
          }, function() {
            $(".imagePreview").remove();
          });
        });
      })
      var waitForEl = function(selector, callback) {
        if (jQuery(selector).length) {
          callback();
        } else {
          setTimeout(function() {
            waitForEl(selector, callback);
          }, 50);
        }
      };
    })
  </script>
<?php
}
add_action("acf/input/admin_head", "fire_acf_admin_head");
