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
  $settings = array();
  fullcalendar_include_api();
  $fullcalendar_options = module_invoke_all('fullcalendar_option_info');
  uasort($fullcalendar_options, 'drupal_sort_weight');
  foreach (array_intersect(array_keys($fullcalendar_options), module_implements('fullcalendar_options_process')) as $module) {
    $function = $module . '_fullcalendar_options_process';
    $function($variables, $settings);
  }

  // Add settings to Drupal.settings.
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
 * Process FullCalendar Colors after the structure is built.
 */
function template_process_fullcalendar(&$variables) {
  if (module_exists('fullcalendar_colors')) {
    fullcalendar_colors_process_selectors(array(), TRUE);
  }
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
      '#markup' => $variables['entity']->title,
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

    if (isset($row->_field_data)) {
      $entity = $row->_field_data[$view->base_field]['entity'];
      $entity_type = $row->_field_data[$view->base_field]['entity_type'];
    }
    else {
      // This should never happen, but just in case.
      return $events;
    }

    list(, , $bundle) = entity_extract_ids($entity_type, $entity);
    $entity->bundle = $bundle;
    $entity->eid = $row->{$view->base_field};
    $entity->entity_type = $entity_type;
    $entity->options = $view->style_options;

    // If the view disallows editing, that's it.
    if (!empty($view->fullcalendar_disallow_editable)) {
      $entity->editable = FALSE;
    }
    // Otherwise, see what other modules think.
    else {
      // Allow resize/drag/drop of an event if user has proper permissions.
      $editable = module_invoke_all('fullcalendar_editable', $entity, $view);
      // If one value is FALSE, return FALSE. The identical operator is needed
      // because of the return value of array_search().
      $editable = array_search(FALSE, $editable, TRUE) === FALSE;
      drupal_alter('fullcalendar_editable', $editable, $entity, $view);
      $entity->editable = $editable;
    }

    // Enable FullCalendar Colors API if needed.
    if (module_exists('fullcalendar_colors')) {
      fullcalendar_colors_include_api();
    }

     // Create a string of class names.
    $classes = array();
    $colors = array();
    foreach (module_implements('fullcalendar_classes') as $module) {
      // Fetch all the class names.
      $function = $module . '_fullcalendar_classes';
      $class_names = $function($entity);

      // Merge for normal classes.
      $classes = array_merge($classes, $class_names);

      // Prepare an array for the FullCalendar Colors module.
      if (module_exists('fullcalendar_colors')) {
        foreach ($class_names as $class_name) {
          $colors[$module][$class_name] = $class_name;
        }
      }
    }

    // Alter the classes array if needed.
    drupal_alter('fullcalendar_classes', $classes, $entity);

    // Add classes to the FullCalendar event.
    $entity->class = implode(' ', array_unique($classes));

    // Call FullCalendar Colors when needed.
    if (module_exists('fullcalendar_colors')) {
      fullcalendar_colors_generate_css($entity, $colors);
    }

    // Default URL.
    $uri = entity_uri($entity_type, $entity);
    $entity->url = $uri['path'];
    // Fetch custom URL if needed.
    if (!empty($options['url'])) {
      $field_name = $options['url_field'];
      if (!empty($fields[$field_name])) {
        $entity->url = ltrim($fields[$field_name], '/');
      }
    }

    // Fetch custom title if needed.
    if (!isset($entity->title)) {
      $entity->title = '';
    }
    if (!empty($options['title'])) {
      $field_name = $options['title_field'];
      if (!empty($fields[$field_name])) {
        $entity->title = $fields[$field_name];
      }
    }

    $event = array();
    foreach ($date_fields as $field) {
      // Filter fields without value.
      if (!empty($field['value'])) {
        $instance = field_info_instance($entity_type, $field['field_name'], $bundle);
        foreach ($field['value'] as $index => $item) {
          $date = date_formatter_process($instance['display']['default']['type'], $entity_type, $entity, $field['field_info'], $instance, LANGUAGE_NONE, $item['raw'], $instance['display']['default']);
          $date1 = $date['value']['local']['object'];
          $date2 = $date['value2']['local']['object'];
          $event[] = array(
            '#theme' => 'link',
            '#text' => $item['rendered']['#markup'],
            '#path' => $entity->url,
            '#options' => array(
              'attributes' => array(
                'allDay' => date_field_all_day($field['field_info'], $instance, $date1, $date2),
                'start' => $date1->format(DATE_FORMAT_DATETIME),
                'end' => $date2->format(DATE_FORMAT_DATETIME),
                'editable' => $entity->editable,
                'field' => $field['field_name'],
                'index' => $index,
                'eid' => $entity->eid,
                'entity_type' => $entity_type,
                'cn' => $entity->class . ' fc-event-field-' . $field['field_name'],
                'title' => strip_tags($entity->title),
                'class' => array('fullcalendar-event-details'),
              ),
              'html' => TRUE,
            ),
          );
        }
      }
    }
    $events[$delta] = array(
      '#theme' => 'fullcalendar_event',
      '#event' => $event,
      '#entity' => $entity,
    );
  }
  return $events;
}
