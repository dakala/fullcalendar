<?php

/**
 * @file
 * Preprocess functions for FullCalendar.
 */


/**
 * Prepares variables for views table templates.
 *
 * Default template: views-view-table.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - view: A ViewExecutable object.
 *   - rows: The raw row data.
 */
function template_preprocess_views_view_fullcalendar(&$variables) {
  $view = $variables['view'];

  $variables['view']->element['#attached'] = $variables['options']['#attached'];
}

/**
 * Builds the FullCalendar structure as a render array.
 */
function template_preprocess_fullcalendar(&$variables) {
  $variables['element'] = [
    '#attached'    => $variables['options']['#attached'],
//    'status'       => [
//      '#type'       => 'container',
//      '#attributes' => [
//        'class' => [
//          'fullcalendar-status',
//        ],
//      ],
//    ],
//    'fullcalendar' => [
//      '#type'       => 'container',
//      '#attributes' => [
//        'class' => [
//          'fullcalendar',
//        ],
//      ],
//    ],
//    'content'      => [
//      '#type'       => 'container',
//      '#attributes' => [
//        'class' => [
//          'fullcalendar-content',
//        ],
//      ],
//      'events'      => $variables['rows'],
//    ],
  ];
}

///**
// * Render the FullCalendar.
// */
//function theme_fullcalendar($variables) {
//  return \Drupal::service('renderer')->render($variables['element']);
//}
//
///**
// * Build the render array for an individual event.
// */
//function template_preprocess_fullcalendar_event(&$variables) {
//  $variables['element'] = [
//    '#type'       => 'container',
//    '#attributes' => [
//      'class' => [
//        'fullcalendar-event',
//      ],
//    ],
//    'title'       => [
//      '#prefix' => '<h3 class="title">',
//      '#suffix' => '</h3>',
//      '#markup' => $variables['entity']->label(),
//    ],
//  ];
//
//  foreach ($variables['event'] as $instance) {
//    $variables['element'][] = [
//      '#type'       => 'container',
//      '#attributes' => [
//        'class' => [
//          'fullcalendar-instance',
//        ],
//      ],
//      [$instance],
//    ];
//  }
//}

///**
// * Render the event.
// */
//function theme_fullcalendar_event($variables) {
//  return \Drupal::service('renderer')->render($variables['element']);
//}
