<?php
// $Id$

/**
 * @file
 * Contains views module hooks
 */

/**
 * Implementation of hook_views_plugins
 */
function fullcalendar_views_plugins() {
  return array(
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
