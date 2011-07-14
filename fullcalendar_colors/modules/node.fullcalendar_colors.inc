<?php

/**
 * @file
 * Implements FullCalendar Colors hooks for node.module.
 */

/**
 * Implements hook_fullcalendar_colors_class_names().
 *
 * Provide colors per node type.
 */
function node_fullcalendar_colors_class_names($entity) {
  $class_names = array();
  if (variable_get('fullcalendar_colors_node_types_enabled', FALSE)) {
    if ($entity->entity_type == 'node') {
      $class_names[] = 'fullcalendar_colors_node_type_' . $entity->type;
    }
  }
  return $class_names;
}
