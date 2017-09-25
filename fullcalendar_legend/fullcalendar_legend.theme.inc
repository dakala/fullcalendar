<?php

/**
 * @file
 * Theme functions for FullCalendar Legend.
 */

use Drupal\Component\Utility\Html;

/**
 * Build the legend as a render array.
 */
function template_preprocess_fullcalendar_legend(&$variables) {
  $element = [
    '#attached' => [
      'css' => [
        drupal_get_path('module', 'fullcalendar_legend') . '/css/fullcalendar_legend.theme.css',
      ],
    ],
  ];

  foreach ($variables['types'] as $type_name => $type) {
    $element[$type_name] = [
      '#type'       => 'container',
      '#attributes' => [
        'class'       => _fullcalendar_legend_get_classes($type),
        'entity_type' => $type['entity_type'],
        'bundle'      => $type['bundle'],
        'field'       => $type['field_name'],
      ],
    ];

    // Sanitize the label.
    $type['label'] = Html::escape($type['label']);

    if (isset($type['uri'])) {
      $element[$type_name]['type'] = [
        '#type'    => 'link',
        '#href'    => $type['uri']['path'],
        '#options' => isset($type['uri']['options']) ? $type['uri']['options'] : [],
        '#title'   => $type['label'],
      ];
    }
    else {
      $element[$type_name]['type'] = [
        '#markup' => $type['label'],
      ];
    }
  }

  $variables['element'] = $element;
}

/**
 * Render the legend.
 */
function theme_fullcalendar_legend($variables) {
  return \Drupal::service('renderer')->renderRoot($variables['element']);
}

/**
 * Spoofs an entity to get its classes.
 *
 * @param array $type
 *   An array containing the following keys:
 *    - "entity_type": The entity type; e.g. 'node' or 'user'.
 *    - "bundle": The bundle name.
 *
 * @return array
 *   An array of CSS classes.
 */
function _fullcalendar_legend_get_classes($type) {
  $values['type'] = $type['bundle'];
  $entity = \Drupal::entityTypeManager()
    ->getStorage($type['entity_type'])
    ->create($values);

  $module_handler = \Drupal::moduleHandler();
  $classes = $module_handler->invokeAll('fullcalendar_classes', [$entity]);
  $module_handler->alter('fullcalendar_classes', $classes, $entity);

  return $classes;
}
