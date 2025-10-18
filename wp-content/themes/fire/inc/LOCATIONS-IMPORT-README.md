# Locations Import Tool

## Overview

This tool allows the `admin-mbm` user to import all location data from JavaScript into WordPress location posts.

## Files

- `fire-locations-importer.php` - The admin page and import logic
- `fire-locations-data.php` - Contains the location data array (NEEDS TO BE COMPLETED)

## How to Complete the Data File

The `fire-locations-data.php` file currently only has a few sample locations. You need to add ALL the location data from your JavaScript file.

### Instructions:

1. Open `inc/fire-locations-data.php`
2. Replace the sample data with ALL locations from your JavaScript file
3. Use this format for each location:

```php
array(
    'title' => 'Bank Name - Branch',
    'address1' => '123 Main Street',
    'address2' => 'City, State ZIP',
    'coords' => array( 'lat' => 12.3456, 'lng' => -78.9012 ),
    'placeId' => 'ChIJ...', // Optional, not used but kept for reference
    'website' => 'https://example.com',
),
```

### Quick Conversion Script

You can use this Node.js script to convert your JavaScript data:

```javascript
const locations = [
  // ... paste your locations array here
];

const phpLocations = locations
  .map((loc) => {
    const website = loc.actions?.find((a) => a.label === 'Website')?.defaultUrl || '';
    return `\t\tarray(
\t\t\t'title' => ${JSON.stringify(loc.title)},
\t\t\t'address1' => ${JSON.stringify(loc.address1)},
\t\t\t'address2' => ${JSON.stringify(loc.address2)},
\t\t\t'coords' => array( 'lat' => ${loc.coords.lat}, 'lng' => ${loc.coords.lng} ),
\t\t\t'placeId' => ${JSON.stringify(loc.placeId)},
\t\t\t'website' => ${JSON.stringify(website)},
\t\t),`;
  })
  .join('\n');

console.log('return array(\n' + phpLocations + '\n\t);');
```

## Using the Importer

1. Log in as `admin-mbm`
2. Go to **Tools** → **Import Locations**
3. Review the current status
4. Click **Import Locations**
5. The tool will:
   - Skip any duplicates (same title + address)
   - Create new location posts
   - Populate all ACF fields
   - Show success message with count

## After Import

Once imported, the locations will automatically appear on your map via the `fire_get_locations_for_map()` function in `fire-locations.php`.

## Safety Features

- Only accessible to `admin-mbm` user
- Duplicate detection (by title + address1)
- Creates published posts immediately
- Shows import count and skipped count
