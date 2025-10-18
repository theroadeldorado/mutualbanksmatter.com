# Location Post Type Setup

This document explains how the new Location post type works and how to use it.

## Overview

The Location post type has been created to manage bank locations that appear on the map. It replaces the previous system that used a static JavaScript file with location data.

## Features

- **Custom Post Type**: "Locations" with a custom icon in the WordPress admin
- **Custom Fields**: Address lines, latitude, longitude, and website URL
- **Auto-Geocoding**: Automatic coordinate lookup using OpenStreetMap API
- **Map Integration**: Locations automatically feed into the Google Maps store locator

## How to Add a New Location

1. Go to **Locations > Add New** in the WordPress admin
2. Enter a **Title** (e.g., "Yakima Federal Savings & Loan Association - Yakima - Stadium")
3. Fill in the **Location Details**:
   - **Address Line 1**: Street address (e.g., "3910 Tieton Dr")
   - **Address Line 2**: City, State, ZIP (e.g., "Yakima, WA 98902, USA")
4. Click the **"Get Coordinates from Address"** button
   - This will automatically fetch the latitude and longitude from OpenStreetMap
   - The button is only enabled when both address lines are filled and coordinates are empty
5. Add a **Website URL** (optional)
   - Include UTM parameters for tracking
   - Example: `https://www.example.com/?utm_source=mutualbanksmatter&utm_medium=referral&utm_campaign=find_location`
6. Click **Publish**

## Field Details

### Address Line 1 (Required)

The street address of the location.

### Address Line 2 (Required)

City, state, and ZIP code. Can include country for international locations.

### Latitude (Required)

The latitude coordinate. Auto-filled by the geocoding button, but can be manually entered if needed.

### Longitude (Required)

The longitude coordinate. Auto-filled by the geocoding button, but can be manually entered if needed.

### Website URL (Optional)

A link to the location's website or specific page. This will appear as a "Website" button on the map.

## Geocoding Button

The "Get Coordinates from Address" button:

- **Enabled when**: Both address lines have values AND coordinates are empty
- **Disabled when**: Address lines are missing OR coordinates already exist
- Uses the [OpenStreetMap Nominatim API](https://nominatim.openstreetmap.org/)
- Shows status messages during and after the geocoding process

### Manual Coordinates

If the auto-geocoding doesn't work or you need to adjust the coordinates:

1. Clear the existing latitude and longitude values
2. The button will become enabled
3. Click to re-geocode, or manually enter coordinates

## Map Integration

The map component automatically:

1. Queries all published locations
2. Sorts them alphabetically by title
3. Formats them for the Google Maps store locator
4. Only includes locations that have valid coordinates

### Map Configuration

The map is configured in `fire-locations.php` with the `fire_get_locations_for_map()` function, which:

- Fetches all published locations
- Filters out any without coordinates
- Formats data in the structure expected by Google Maps
- Returns a sorted array of locations

## Files Modified/Created

### New Files:

- `/inc/fire-locations.php` - Location helper functions (map data formatting, admin scripts)
- `/inc/fire-location-geocode.js` - Geocoding button JavaScript
- `/acf-json/post_type_location.json` - **ACF post type registration** (Location CPT)
- `/acf-json/group_location_fields.json` - ACF field group definition

### Modified Files:

- `/templates/components/map/map.php` - Now pulls data from locations post type
- `/templates/components/map/map.js` - Accepts locations as parameter

### How It Works:

The Location post type is registered entirely through ACF (not via PHP). ACF automatically loads the post type from the JSON file on initialization. The PHP file only contains helper functions for formatting location data and enqueueing admin scripts.

## API Usage

The system uses the OpenStreetMap Nominatim API for geocoding. This is a free service with the following considerations:

- **Rate Limit**: 1 request per second
- **User-Agent**: Set to "MutualBanksMatter/1.0"
- **No API Key Required**: The service is free and open

## Troubleshooting

### Geocoding Button Doesn't Work

- Check browser console for errors
- Verify both address lines are filled correctly
- Try a more specific address (include full state name, ZIP code)
- Check internet connection

### Location Doesn't Appear on Map

- Verify the location is published (not draft)
- Ensure latitude and longitude have values
- Check that coordinates are valid numbers
- Clear any caching (page cache, object cache)

### Coordinates Are Incorrect

- Clear the latitude and longitude fields
- Re-geocode with the button
- Or manually enter the correct coordinates

## Future Enhancements

Possible additions:

- Bulk import from CSV
- Additional action buttons (directions, phone, etc.)
- Location categories/taxonomy
- Custom map markers per location type
- Opening hours field
- Phone number field
