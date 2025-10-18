/**
 * Location Geocoding functionality
 * Uses OpenStreetMap Nominatim API to get coordinates from address
 */
(function ($) {
  // Wait for ACF to be ready
  if (typeof acf !== 'undefined') {
    acf.addAction('ready', () => {
      initGeocodeButton();
    });
  } else {
    $(document).ready(() => {
      initGeocodeButton();
    });
  }

  function initGeocodeButton() {
    const $searchButton = $('#fire-address-search-button');
    const $searchStatus = $('.fire-search-status');

    if ($searchButton.length === 0) {
      return;
    }

    // Handle address search button
    $searchButton.on('click', (e) => {
      e.preventDefault();
      searchAndAutoFill();
    });

    function searchAndAutoFill() {
      const searchValue = $('[data-name="address_search"] input').val().trim();

      if (!searchValue) {
        showSearchError('Please enter an address to search');
        return;
      }

      // Disable button and show loading state
      $searchButton.prop('disabled', true).text('Searching...');
      $searchStatus.html('<span style="color: #999; margin-left: 10px;">Searching...</span>');

      geocodeWithOpenStreetMap(searchValue);
    }

    function geocodeWithOpenStreetMap(address) {
      const apiUrl = 'https://nominatim.openstreetmap.org/search';
      const params = {
        q: address,
        format: 'json',
        limit: 1,
        addressdetails: 1,
      };
      $.ajax({
        url: apiUrl,
        method: 'GET',
        data: params,
        success(response) {
          if (response && response.length > 0) {
            const location = response[0];
            const lat = parseFloat(location.lat);
            const lng = parseFloat(location.lon);
            const addr = location.address || {};

            // Auto-fill all fields from search
            const street = `${addr.house_number || ''} ${addr.road || ''}`.trim();
            const cityState = `${addr.city || addr.town || addr.village || ''}, ${addr.state || ''} ${addr.postcode || ''}`.trim();

            $('[data-name="address_line_1"] input').val(street).trigger('change');
            $('[data-name="address_line_2"] input').val(cityState).trigger('change');
            $('[data-name="latitude"] input').val(lat).trigger('change');
            $('[data-name="longitude"] input').val(lng).trigger('change');

            $searchStatus.html('<span style="color: #46b450; margin-left: 10px;">✓ Address auto-filled!</span>');
            $searchButton.prop('disabled', false).text('Search & Auto-Fill Address');
          } else {
            showSearchError('Address not found. Try being more specific or manually enter the address.');
          }
        },
        error(xhr) {
          if (xhr.status === 429) {
            showSearchError('Rate limit reached. Please wait 1 second and try again.');
          } else {
            showSearchError('Network error. Please check your internet connection.');
          }
        },
      });
    }

    function showSearchError(message) {
      $searchStatus.html(`<span style="color: #dc3232; margin-left: 10px;">✗ ${message}</span>`);
      $searchButton.prop('disabled', false).text('Search & Auto-Fill Address');
    }
  }
})(jQuery);
