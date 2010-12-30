This module requires the 3rd party library for FullCalendar located at
http://arshaw.com/fullcalendar. Download and unzip this library into the
sites/all/libraries/fullcalendar directory, or use the Libraries API module for
site specific libraries.

This version of FullCalendar requires specific versions of the following:
  The 6.x-2.x branch of jQuery Update (http://drupal.org/project/jquery_update)
  Version 1.7 of jQuery UI (http://drupal.org/project/jquery_ui)
Instructions for installing each can be found in their respective README.txt files

To use the fullcalendar module:
  1) Install Views, Date, Date API, and Date Timezone modules
  2) Create a new content type that has either a date or a datetime field
  3) Create a view for the content type
  4) Change the view style plugin to "FullCalendar"
  5) Change the view row style plugin to "Node - FullCalendar"

KNOWN ISSUES:
When displaying a repeating date field, each event might show up multiple times
on each repeat instance. To solve this problem, change the 'Distinct' setting
to yes in the Views display settings. This is a not a bug in FullCalendar.
