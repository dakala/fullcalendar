<?php
// $Id$

/**
 * @file
 * Contains views module hooks
 */

/**
 * Implements hook_views_plugins().
 */
function fullcalendar_views_plugins() {
  $path = drupal_get_path('module', 'fullcalendar');
  return array(
    'module' => 'fullcalendar',
    'style' => array(
      'fullcalendar' => array(
      'title' => t('FullCalendar'),
      'help' => t('Displays items on a calendar.'),
      'handler' => 'views_plugin_style_fullcalendar',
      'theme' => 'views_view_fullcalendar',
      'theme path' => $path,
      'uses row plugin' => TRUE,
      'uses options' => TRUE,
      'uses grouping' => TRUE,
      'type' => 'normal',
      'parent' => 'default',
      ),
    ),
    'row' => array(
      'fullcalendar_node' => array(
        'title' => t('Fields (FullCalendar)'),
        'help' => t('For use with FullCalendar style'),
        'handler' => 'views_plugin_node_fullcalendar',
        'theme' => 'views_view_node_fullcalendar',
        'theme path' => $path,
        'uses fields' => TRUE,
        'uses options' => TRUE,
        'type' => 'normal',
      ),
    ),
  );
}

/**
 * Implementation of hook_views_data()
 */
function fullcalendar_views_data() {

  $data['fullcalendar'] = array(
    'table' => array(
      'group' => t('FullCalendar'),
      'join' => array('#global' => array()),
    ),
    'area' => array(
      'title' => t('Emtpy calendar'),
      'help' => t('Provide empty calendar.'),
      'area' => array(
        'handler' => 'fullcalendar_handler_empty',
      ),
    ),
  );

  return $data;
}
