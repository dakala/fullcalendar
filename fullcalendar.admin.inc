<?php
// $Id$

/**
 * @file
 * Administrative page callbacks for the fullcalendar module.
 */

/**
 * General configuration form for controlling the fullcalendar behaviour.
 */
function fullcalendar_admin_settings() {
  $fullcalendar_path = variable_get('fullcalendar_path', FULLCALENDAR_PATH);

  $form['fullcalendar_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to FullCalendar'),
    '#default_value' => $fullcalendar_path,
    '#description' => t('Enter the path relative to Drupal root where the FullCalendar plugin directory is located. NO trailing slash!'),
  );
  $form['fullcalendar_compression_type'] = array(
    '#type' => 'radios',
    '#title' => t('Choose FullCalendar compression level'),
    '#options' => array(
      'min' => t('Production (Minified)'),
      'none' => t('Development (Uncompressed Code)'),
    ),
    '#default_value' => variable_get('fullcalendar_compression_type', 'min'),
  );

  return system_settings_form($form);
}
