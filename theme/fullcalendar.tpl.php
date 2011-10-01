<?php

/**
 * @file
 * View to display the FullCalendar.
 *
 * Variables available:
 * - $rows: The results of the view query, if any
 */

?>
<div class="fullcalendar-status"></div>
<div class="fullcalendar"></div>
<div class="fullcalendar-content">
<?php foreach ($rows as $event): ?>
  <?php if (!empty($event)): ?>
  <div class="fullcalendar-event">
    <?php print $event; ?>
  </div>
  <?php endif; ?>
<?php endforeach; ?>
</div>
