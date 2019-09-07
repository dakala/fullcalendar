<?php

/**
 * @file
 * Contains Views module hooks.
 */

/**
 * Implements hook_views_data().
 */
function fullcalendar_views_data() {
  $data = [];

  $data['fullcalendar'] = [
    'table' => [
      'group' => t('FullCalendar'),
      'join'  => [
        '#global' => [],
      ],
    ],
    'gcal'  => [
      'title' => t('Google Calendar'),
      'help'  => t('Display events from a Google Calendar.'),
      'field' => [
        'id' => 'fullcalendar_gcal',
      ],
    ],
  ];

  return $data;
}
