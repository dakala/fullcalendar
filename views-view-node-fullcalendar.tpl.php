<?php
// $Id$
/**
 * @file views-view-node-fullcalendar.tpl.php
 * View to display the fullcalendar rows (events)
 *
 * Variables available:
 * - $url: The url for the event
 * - $title : The event node's title
 * - $allDay : If the event is all day (does not include hour and minute granularity)
 * - $start : When the event start
 * - $end : When the event ends
 */
 
?>
<div class="fullcalendar_event">
  <a class="fullcalendar_event_details" href="<?php echo $url; ?>" title="<?php echo $title; ?>" allDay="<?php echo $allDay; ?>" start="<?php echo $start; ?>" end="<?php echo $end; ?>"><?php echo $title; ?></a> : <?php echo format_date(strtotime($start)); ?>
  <?php if((!$allDay) and ($end)){ ?>
    to <?php echo format_date(strtotime($end)); ?>
  <?php } ?>
</div>