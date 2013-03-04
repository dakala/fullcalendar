<?php

/**
 * @file
 * Preprocess functions for FullCalendar.
 */

/**
 * Builds the FullCalendar structure as a render array.
 */
function template_preprocess_fullcalendar(&$variables) {
  $events = $variables['rows'];

  // If we're using ajax, we're done.
  if (!empty($variables['view']->fullcalendar_ajax)) {
    $variables['element'] = $events;
    return;
  }

  $variables['element'] = array(
    'status' => array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array(
          'fullcalendar-status',
        ),
      ),
    ),
    'fullcalendar' => array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array(
          'fullcalendar',
        ),
      ),
    ),
    'content' => array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array(
          'fullcalendar-content',
        ),
      ),
    ),
  );


  if ($events) {
    $variables['element']['content']['events'] = $events;
  }

  // Gather options from all modules.
  $weights = array();
  $delta = 0;
  $settings = array();
  foreach ($variables['view']->style_plugin->getPlugins() as $plugin_id => $plugin) {
    $definition = $plugin->getDefinition();
    $plugin->process($variables, $settings);
    if (isset($definition['weight']) && !isset($weights[$definition['weight']])) {
      $weights[$definition['weight']] = $plugin_id;
    }
    else {
      while (isset($weights[$delta])) {
        $delta++;
      }
      $weights[$delta] = $plugin_id;
    }

    foreach (array('css', 'js') as $type) {
      if ($definition[$type]) {
        $variables['element']['#attached'][$type][] = drupal_get_path('module', $definition['module']) . "/$type/$plugin_id.fullcalendar.$type";
      }
    }
  }
  ksort($weights);
  $settings['weights'] = array_values($weights);
  // @todo.
  $settings['fullcalendar']['disableResizing'] = TRUE;

  // Add settings to Drupal.settings.
  // Add JS and CSS files.
  $variables['element']['#attached']['library'][] = array('fullcalendar', 'fullcalendar-module');

  if (!empty($settings['ajax'])) {
    $variables['element']['#attached']['js'][] = drupal_get_path('module', 'fullcalendar') . '/js/fullcalendar.ajax.js';
  }
  $variables['element']['#attached']['js'][] = array(
    'type' => 'setting',
    'data' => array(
      'fullcalendar' => array(
        '.view-dom-id-' . $variables['view']->dom_id => $settings,
      ),
    ),
  );
}

/**
 * Render the FullCalendar.
 */
function theme_fullcalendar($variables) {
  return drupal_render($variables['element']);
}

/**
 * Build the render array for an individual event.
 */
function template_preprocess_fullcalendar_event(&$variables) {
  $variables['element'] = array(
    '#type' => 'container',
    '#attributes' => array(
      'class' => array(
        'fullcalendar-event',
      ),
    ),
    'title' => array(
      '#prefix' => '<h3 class="title">',
      '#suffix' => '</h3>',
      '#markup' => $variables['entity']->label(),
    ),
  );
  foreach ($variables['event'] as $instance) {
    $variables['element'][] = array(
      '#type' => 'container',
      '#attributes' => array(
        'class' => array(
          'fullcalendar-instance',
        ),
      ),
      array($instance),
    );
  }
}

/**
 * Render the event.
 */
function theme_fullcalendar_event($variables) {
  return drupal_render($variables['element']);
}
