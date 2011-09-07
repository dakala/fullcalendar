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
        'uses row plugin' => TRUE,
        'uses options' => TRUE,
        'uses grouping' => TRUE,
        'type' => 'normal',
      ),
    ),
    'row' => array(
      'fullcalendar_node' => array(
        'title' => t('Fields (FullCalendar)'),
        'help' => t('For use with FullCalendar style'),
        'handler' => 'fullcalendar_plugin_row_fields',
        'path' => $path,
        'theme' => 'fullcalendar_fields',
        'theme file' => 'theme.inc',
        'theme path' => $theme_path,
        'uses fields' => TRUE,
        'uses options' => TRUE,
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
 * Implements hook_views_data_alter().
 *
 * Find all date fields and assign them our handler.
 */
function fullcalendar_views_data_alter(&$data) {
  // Field API date fields.
  foreach (field_info_fields() as $id => $field) {
    if (in_array($field['type'], array('date', 'datestamp', 'datetime'))) {
      $data['field_data_' . $id][$id]['field']['handler'] = 'fullcalendar_handler_field_field';
    }
  }
  // UNIX timestamps.
  foreach ($data as $module => &$table) {
    foreach ($table as $id => &$field) {
      if (isset($field['field']) && isset($field['field']['handler']) && ($field['field']['handler'] == 'views_handler_field_date')) {
        $field['field']['handler'] = 'fullcalendar_handler_field_date';
      }
    }
  }
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

  foreach ($view->display_handler->get_handlers('field') as $field_name => $field) {
    // If the field is a date field, its handler will have this method.
    if (!method_exists($field, 'fullcalendar_render')) {
      continue;
    }

    // Set the argument to provide a default value.
    $options = array('default_action' => 'default');

    // Check to see if this is a Field API field, or a timestamp.
    $info = field_info_field($field_name);
    if (!empty($info)) {
      $table = 'field_data_' . $field_name;
      $field_col = $field_name . '_value';
    }
    else {
      $table = $field->table;
      $field_col = $field_name . '_year_month';
      $options['default_argument_type'] = 'date';
    }

    // Add an argument for the date field.
    $view->add_item($display_id, 'argument', $table, $field_col, $options);
  }
}
