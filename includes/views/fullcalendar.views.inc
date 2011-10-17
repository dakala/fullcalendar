<?php

/**
 * @file
 * Contains views module hooks
 */

/**
 * Implements hook_views_plugins().
 */
function fullcalendar_views_plugins() {
  $module_path = drupal_get_path('module', 'fullcalendar');
  $theme_path = $module_path . '/theme';
  $path = $module_path . '/includes/views/plugins';
  $views_path = drupal_get_path('module', 'views');
  return array(
    'module' => 'fullcalendar',
    'display' => array(
      'fullcalendar' => array(
        'title' => t('FullCalendar'),
        'handler' => 'fullcalendar_plugin_display_page',
        'help' => t('For use with FullCalendar style'),
        'path' => $path,
        'parent' => 'page',
        'theme' => 'views_view',
        'uses hook menu' => TRUE,
        'use pager' => TRUE,
      ),
    ),
    'style' => array(
      'fullcalendar' => array(
        'title' => t('FullCalendar'),
        'help' => t('Displays items on a calendar.'),
        'handler' => 'fullcalendar_plugin_style_fullcalendar',
        'path' => $path,
        'theme' => 'fullcalendar',
        'theme file' => 'theme.inc',
        'theme path' => $theme_path,
        'uses row plugin' => FALSE,
        'uses fields' => TRUE,
        'uses options' => TRUE,
        'uses grouping' => TRUE,
        'even empty' => TRUE,
        'type' => 'normal',
      ),
    ),
  );
}

/**
 * Implements hook_views_data().
 */
function fullcalendar_views_data() {

  $data['fullcalendar'] = array(
    'table' => array(
      'group' => t('FullCalendar'),
      'join' => array('#global' => array()),
    ),
    'area' => array(
      'title' => t('Empty calendar'),
      'help' => t('Provide empty calendar.'),
      'area' => array(
        'handler' => 'fullcalendar_handler_area_empty',
      ),
    ),
    'gcal' => array(
      'title' => t('Google Calendar'),
      'help' => t('Display events from a Google Calendar.'),
      'field' => array(
        'handler' => 'fullcalendar_handler_field_gcal',
      ),
    ),
  );

  return $data;
}

/**
 * Implements hook_views_pre_view().
 *
 * Add an argument that provides the current date for each date field present.
 */
function fullcalendar_views_pre_view(&$view, &$display_id, &$args) {
  if ($view->display_handler->get_option('style_plugin') != 'fullcalendar' || !$view->display_handler->get_option('use_ajax')) {
    return;
  }

  foreach ($view->display_handler->get_handlers('field') as $field) {
    // If the field is a date field, its handler will have this method.
    if (fullcalendar_field_is_date($field)) {
      // Add an argument for the date field.
      $view->add_item($display_id, 'argument', 'field_data_' . $field->field, $field->field . '_value', array('default_action' => 'default'));
      $view->fullcalendar_args++;
    }
  }
}

/**
 * Implements hook_views_query_alter().
 *
 * Force the query to be distinct. Also, if there are multiple date arguments,
 * group them with 'OR'.
 */
function fullcalendar_views_query_alter(&$view, &$query) {
  if ($view->display_handler->get_option('style_plugin') != 'fullcalendar') {
    return;
  }

  $query->distinct = TRUE;

  if (empty($view->fullcalendar_args) || count($view->args) < $view->fullcalendar_args) {
    return;
  }

  // Remove our arguments from the main set.
  $arguments = array();
  for ($i = 0; $i < $view->fullcalendar_args; $i++) {
    $arguments[] = array_pop($query->where[0]['conditions']);
  }
  // Add them as a second set of conditions.
  $query->where[] = array(
    'conditions' => $arguments,
    'args' => array(),
    'type' => 'OR',
  );
}
