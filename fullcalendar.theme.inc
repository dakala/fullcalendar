<?php

/**
 * @file
 * Preprocess functions for FullCalendar.
 */

/**
 * Builds the FullCalendar structure as a render array.
 */
function template_preprocess_fullcalendar(&$variables) {
  $variables['element'] = [
    '#attached'    => $variables['options']['#attached'],
    'status'       => [
      '#type'       => 'container',
      '#attributes' => [
        'class' => [
          'fullcalendar-status',
        ],
      ],
    ],
    'fullcalendar' => [
      '#type'       => 'container',
      '#attributes' => [
        'class' => [
          'fullcalendar',
        ],
      ],
    ],
    'content'      => [
      '#type'       => 'container',
      '#attributes' => [
        'class' => [
          'fullcalendar-content',
        ],
      ],
      'events'      => $variables['rows'],
    ],
  ];
}

/**
 * Render the FullCalendar.
 */
function theme_fullcalendar($variables) {
  return \Drupal::service('renderer')->render($variables['element']);
}

/**
 * Build the render array for an individual event.
 */
function template_preprocess_fullcalendar_event(&$variables) {
  $variables['element'] = [
    '#type'       => 'container',
    '#attributes' => [
      'class' => [
        'fullcalendar-event',
      ],
    ],
    'title'       => [
      '#prefix' => '<h3 class="title">',
      '#suffix' => '</h3>',
      '#markup' => $variables['entity']->label(),
    ],
  ];

  foreach ($variables['event'] as $instance) {
    $variables['element'][] = [
      '#type'       => 'container',
      '#attributes' => [
        'class' => [
          'fullcalendar-instance',
        ],
      ],
      [$instance],
    ];
  }
}

/**
 * Render the event.
 */
function theme_fullcalendar_event($variables) {
  return \Drupal::service('renderer')->render($variables['element']);
}
