<?php

/**
 * @file
 * Hooks provided by the FullCalendar Colors module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Fetches the needed css classes for coloring the fullcalendar.
 *
 * @param $entity
 *   Object representing the entity.
 *
 * @return $class_names
 *   Array containing the class that will be processed by fullcalendar for each
 *   event.
 */
function hook_fullcalendar_colors_class_names($entity) {
  $class_names = array();
  $class_names[] = 'my_awesome_class_name';
  $class_names[] = 'another_awesome_class_name';
  return $class_names;
}

/**
 * @} End of "addtogroup hooks".
 */
