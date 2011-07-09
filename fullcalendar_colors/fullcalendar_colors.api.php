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
 * Fetches the needed CSS classes for coloring the FullCalendar.
 *
 * @param $entity
 *   Object representing the entity.
 *
 * @return
 *   Array of classes that will be processed by FullCalendar for each entity.
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
