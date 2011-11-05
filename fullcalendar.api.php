<?php

/**
 * @file
 * Hooks provided by the FullCalendar module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Constructs CSS classes for an entity.
 *
 * @param $entity
 *   Object representing the entity.
 *
 * @return
 *   Array of CSS classes.
 */
function hook_fullcalendar_classes($entity) {
  // Add the entity type as a class.
  return array(
    $entity->entity_type,
  );
}

/**
 * Alter the CSS classes for an entity.
 *
 * @param $classes
 *   Array of CSS classes.
 * @param $entity
 *   Object representing the entity.
 *
 */
function hook_fullcalendar_classes_alter(&$classes, $entity) {
  // Remove all classes set by modules.
  $classes = array();
}

/**
 * Declare that you provide a droppable callback.
 *
 * Implementing this hook will cause a checkbox to appear on the view settings,
 * when checked FullCalendar will search for JS callbacks in the form
 * Drupal.fullCalendar.droppableCallbacks.MODULENAME.callback.
 *
 * @see http://arshaw.com/fullcalendar/docs/dropping/droppable
 */
function hook_fullcalendar_droppable() {
  // This hook will never be executed.
  return TRUE;
}

/**
 * Allows your module to affect the editability of the calendar.
 *
 * If any module implementing this hook returns FALSE, the value will be set to
 * FALSE. Use hook_fullcalendar_editable_alter() to override this if necessary.
 *
 * @param $entity
 *   Object representing the entity.
 * @param $view
 *   Object representing the view.
 *
 * @return
 *   A boolean value dictating whether of not the calendar is editable.
 */
function hook_fullcalendar_editable($entity, $view) {
   return _fullcalendar_update_access($entity);
}

/**
 * Allows your module to forcibly override the editability of the calendar.
 *
 * @param $editable
 *   A boolean value dictating whether of not the calendar is editable.
 * @param $entity
 *   Object representing the entity.
 * @param $view
 *   Object representing the view.
 */
function hook_fullcalendar_editable_alter(&$editable, $entity, $view) {
  $editable = FALSE;
}

/**
 * @} End of "addtogroup hooks".
 */
