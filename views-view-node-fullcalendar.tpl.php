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
 * - $className : The node type that the event came from
 *
 * Note that if you use className for the event's className attribute then you'll get weird results from jquery!
 */
 
?>
<div class="fullcalendar_event">
  <a class="fullcalendar_event_details" cn="<?php echo $className; ?>" href="<?php echo $url; ?>" title="<?php echo $title; ?>" allDay="<?php echo $allDay; ?>" start="<?php echo $start; ?>" end="<?php echo $end; ?>"><?php echo $title; ?></a> : <?php echo format_date(strtotime($start)); ?>
  <?php if((!$allDay) and ($end)){ ?>
    to <?php echo format_date(strtotime($end)); ?>
  <?php } ?>
</div>