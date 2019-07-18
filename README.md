This module implements a Drupal integration of the [FullCalendar](https://fullcalendar.io/) the most popular full-sized JavaScript calendar. This Drupal module is only compatible with the v3.x version of FullCalendar.

# Installation

There are 2 ways of installing the Drupal module and the 3rd party FullCalendar library.

## Composer

1. Run composer require `drupal/fullcalendar` to download the Drupal module to the `modules/contrib` folder.
2. Run `composer require --prefer-dist composer/installers` to ensure that you have the composer/installers package installed. This package facilitates the installation of packages into directories other than /vendor (e.g. /libraries) using Composer.
3. Add the following snippet to the `repositories` section of the `composer.json` file in your Drupal root folder:

```
	{
    "type": "package",
    "package": {
        "name": "fullcalendar/fullcalendar",
        "version": "3.9.0",
        "type": "drupal-library",
        "dist": {
            "url": "https://github.com/fullcalendar/fullcalendar/archive/v3.9.0.zip",
            "type": "zip"
        }
    }
}
```

4. Run `composer require --prefer-dist fullcalendar/fullcalendar:3.9.*` and composer will download the Fullcalendar library to the right location


## Manual

1. Download [Fullcalendar module](https://drupal.org/project) and unpack it inside the `modules/contrib` folder.
2. Download the [FullCalendar library](https://github.com/fullcalendar/fullcalendar/releases/tag/v3.9.0) and unpack it to `libraries/fullcalendar` inside your Drupal web root. When unzipped, the library  When unzipped, the library contains several directories. The fullcalendar/fullcalendar directory should be moved to /libraries/fullcalendar i.e. in the root of your Drupal installation. (e.g., /libraries/fullcalendar/fullcalendar.min.js). You may not include the demos or jQuery directories.

# Usage

  1. Enable Views, Date and Date Range modules.
  2. Create a new entity that has a date field.
  3. Create a view and add filters for the entity.
  4. In the "Format" section, change the "Format" to "FullCalendar".
  5. Enable the "Use AJAX" option under "Advanced" (Optional).
