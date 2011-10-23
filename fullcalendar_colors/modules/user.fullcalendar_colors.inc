<?php

/**
 * @file
 * Implements FullCalendar Colors hooks for user.module.
 */

/**
 * Implements hook_fullcalendar_classes().
 *
 * Provide colors per user role.
 */
function user_fullcalendar_classes($entity) {
  $class_names = array();
  if (variable_get('fullcalendar_colors_user_roles_enabled', FALSE)) {
    if ($entity->entity_type == 'user') {
      foreach ($entity->roles as $role_id => $role) {
        $class_names[] = 'fullcalendar_colors_user_role_' . $role_id;
      }
    }
  }
  return $class_names;
}
