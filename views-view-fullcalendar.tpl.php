<?php
// $Id$

/**
 * @file
 * View to display the fullcalendar
 *
 * Variables available:
 * - $rows: The results of the view query, if any
 * - $options: Options for the fullcalendar style plugin
 * -   fullcalendar_view : the default view for the calendar
 * -   fullcalendar_url_colorbox : whether or not to attempt to use colorbox to open events
 * -   fullcalendar_theme : whether or not to apply a loaded jquery-ui theme
 * -   fullcalendar_header_left : values for the header left region : http://arshaw.com/fullcalendar/docs/display/header/
 * -   fullcalendar_header_center : values for the header center region : http://arshaw.com/fullcalendar/docs/display/header/
 * -   fullcalendar_header_right : values for the header right region : http://arshaw.com/fullcalendar/docs/display/header/
 * -   fullcalendar_weekmode : number of week rows : http://arshaw.com/fullcalendar/docs/display/weekMode/
 */

?>
<div id="fullcalendar-status"></div>
<div id="fullcalendar"></div>
<div id="fullcalendar-content">
<?php foreach ($rows as $event): ?>
  <?php if (!empty($event)): ?>
  <div class="fullcalendar_event">
    <?php print $event; ?>
  </div>
  <?php endif; ?>
<?php endforeach; ?>
</div>
