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
 * These classes will NOT be added to the FullCalendar and are ONLY usefull if
 * there is an existing class you want to color (can be useful for coloring
 * gcal calendars).
 *
 * If you want to add classes AND color at the same time you should use
 * hook_fullcalendar_classes().
 *
 * @param object $entity
 *   Object representing the entity.
 *
 * @return array
 *   Array of classes that will be processed by FullCalendar for each entity.
 */
function hook_fullcalendar_colors_css_classes($entity) {
  $class_names = array();
  $class_names[] = 'my_awesome_class_name';
  $class_names[] = 'another_awesome_class_name';
  return $class_names;
}

/**
 * @} End of "addtogroup hooks".
 */
