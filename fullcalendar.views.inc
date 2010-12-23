<?php
// $Id$

/**
 * @file
 * Contains views module hooks
 */

/**
 * Implementation of hook_views_plugins().
 */
function fullcalendar_views_plugins() {
  $path = drupal_get_path('module', 'fullcalendar');
  $views_path = drupal_get_path('module', 'views');
  return array(
    'display' => array(
      'parent' => array(
        'no ui' => TRUE,
        'handler' => 'views_plugin_display',
        'path' => "$views_path/plugins",
        'parent' => '',
      ),
      'page' => array(
        'handler' => 'views_plugin_display_page',
        'path' => "$views_path/plugins",
        'parent' => 'parent',
      ),
      'fullcalendar' => array(
        'title' => t('FullCalendar'),
        'handler' => 'fullcalendar_plugin_display_page',
        'help' => t('For use with FullCalendar style'),
        'path' => $path,
        'parent' => 'page',
        'theme' => 'views_view',
        'uses hook menu' => TRUE,
      ),
    ),
    'style' => array(
      'fullcalendar' => array(
      'title' => t('FullCalendar'),
      'help' => t('Displays items on a calendar.'),
      'handler' => 'views_plugin_style_fullcalendar',
      'theme' => 'views_view_fullcalendar',
      'uses row plugin' => TRUE,
      'uses options' => TRUE,
      'uses grouping' => TRUE,
      'type' => 'normal',
      'parent' => 'default',
      ),
    ),
    'row' => array(
      'parent' => array(
        'no ui' => TRUE,
        'handler' => 'views_plugin_row',
        'parent' => '',
      ),
      'fullcalendar_node' => array(
        'title' => t('Node - FullCalendar'),
        'help' => t('For use with FullCalendar style'),
        'handler' => 'views_plugin_node_fullcalendar',
        'theme' => 'views_view_node_fullcalendar',
        'uses fields' => FALSE,
        'uses options' => TRUE,
        'type' => 'normal',
      ),
    ),
  );
}
