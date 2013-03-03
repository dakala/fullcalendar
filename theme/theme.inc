<?php

/**
 * @file
 * Preprocess functions for FullCalendar.
 */

/**
 * Builds the FullCalendar structure as a render array.
 */
function template_preprocess_fullcalendar(&$variables) {
  $events = fullcalendar_prepare_events($variables['view'], $variables['rows'], $variables['options']['fields']);

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
  $manager = drupal_container()->get('plugin.manager.fullcalendar');
  foreach ($manager->getDefinitions() as $plugin_id => $definition) {
    $plugin = $manager->createInstance($plugin_id);
    $plugin->process($variables, $settings);
    if (isset($definition['weight'])) {
      if (!isset($weights[$definition['weight']])) {
        $weights[$definition['weight']] = $plugin_id;
      }
      else {
        while (isset($weights[$delta])) {
          $delta++;
        }
        $weights[$delta] = $plugin_id;
      }
    }
  }
  ksort($weights);
  $settings['weights'] = array_values($weights);

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

/**
 * Build a render array representing the events.
 *
 * @param object $view
 *   The view object.
 * @param array $rows
 *   An array of row objects.
 * @param array $options
 *   An array of options from the style plugin.
 *
 * @return array
 *   A render array of events.
 */
function fullcalendar_prepare_events($view, $rows, $options) {
  if (empty($rows)) {
    return;
  }

  $events = array();
  foreach ($rows as $delta => $row) {
    // Collect all fields for the customize options.
    $fields = array();
    // Collect only date fields.
    $date_fields = array();
    foreach ($view->field as $field_name => $field) {
      $fields[$field_name] = $view->style_plugin->get_field($delta, $field_name);
      if (fullcalendar_field_is_date($field)) {
        $date_fields[$field_name] = array(
          'value' => $field->get_items($row),
          'field_alias' => $field->field_alias,
          'field_name' => $field->field_info['field_name'],
          'field_info' => $field->field_info,
        );
      }
    }

    // If using a custom date field, filter the fields to process.
    if (!empty($options['date'])) {
      $date_fields = array_intersect_key($date_fields, $options['date_field']);
    }

    // If there are no date fields (gcal only), return.
    if (empty($date_fields)) {
      return $events;
    }

    $entity = $row->_entity;
    $classes = module_invoke_all('fullcalendar_classes', $entity);
    drupal_alter('fullcalendar_classes', $classes, $entity);
    $classes = array_map('drupal_html_class', $classes);
    $class = implode(' ', array_unique($classes));

    $event = array();
    foreach ($date_fields as $field) {
      // Filter fields without value.
      if (!empty($field['value'])) {
        $instance = field_info_instance($entity->entityType(), $field['field_name'], $entity->bundle());
        foreach ($field['value'] as $index => $item) {
          $start = $item['raw']['value'];
          $end = $start;
          $all_day = FALSE;
          $uri = $entity->uri();
          $event[] = array(
            '#theme' => 'link',
            '#text' => $item['raw']['value'],
            '#path' => $uri['path'],
            '#options' => array(
              'attributes' => array(
                'allDay' => $all_day,
                'start' => $start,
                'end' => $end,
                'editable' => TRUE,//$entity->editable,
                'field' => $field['field_name'],
                'index' => $index,
                'eid' => $entity->id(),
                'entity_type' => $entity->entityType(),
                'cn' => $class,
                'title' => strip_tags(htmlspecialchars_decode($entity->label(), ENT_QUOTES)),
                'class' => array('fullcalendar-event-details'),
              ),
              'html' => TRUE,
            ),
          );
        }
      }
    }

    if (!empty($event)) {
      $events[$delta] = array(
        '#theme' => 'fullcalendar_event',
        '#event' => $event,
        '#entity' => $entity,
      );
    }
  }
  return $events;
}
