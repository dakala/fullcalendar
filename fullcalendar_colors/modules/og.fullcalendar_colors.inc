 <?php

/**
 * @file
 * Implements FullCalendar Colors hooks for node.module.
 */

/**
 * Implements hook_fullcalendar_classes().
 *
 * Provide colors per og type.
 */
function og_fullcalendar_classes($entity) {
  $class_names = array();
  $gids = og_get_entity_groups($entity->entity_type, $entity);
  foreach ($gids as $gid) {
    $class_names[] = 'fullcalendar_color_group_' . $gid;
  }
  return $class_names;
}
