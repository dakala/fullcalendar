<?php

/**
 * @file
 * Preprocess functions for FullCalendar.
 */

/**
 * Builds the FullCalendar structure as a render array.
 */
function template_preprocess_fullcalendar(&$variables) {
  fullcalendar_get_settings($variables['view']);

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

  if (empty($variables['rows'])) {
    return;
  }

  $events = fullcalendar_prepare_events($variables['view'], $variables['rows'], $variables['options']['fields']);

  if (!empty($variables['view']->fullcalendar_ajax)) {
    $variables['element'] = $events;
  }
  else {
    $variables['element']['content']['events'] = $events;
  }
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
 * @param $view
 *   The view object.
 * @param $rows
 *   An array of row objects.
 * @param $options
 *   An array of options from the style plugin.
 *
 * @return
 *   A render array of events.
 */
function fullcalendar_prepare_events($view, $rows, $options) {
  $events = array();
  foreach ($rows as $delta => $row) {
    $fields = array();
    $date_fields = array();
    foreach ($view->field as $field_name => $field) {
      $fields[$field_name] = $view->style_plugin->get_field($delta, $field_name);
      if (method_exists($field, 'fullcalendar_render')) {
        $date_fields[$field_name] = $field->fullcalendar_render($row);
      }
    }
    $eid = $row->{$view->base_field};
    if (isset($row->_field_data)) {
      $entity = $row->_field_data[$view->base_field]['entity'];
      $entity_type = $row->_field_data[$view->base_field]['entity_type'];
    }
    else {
      foreach (entity_get_info() as $entity_type => $entity_info) {
        if ($entity_info['base table'] == $view->base_table) {
          break;
        }
      }
      $entity = entity_load($entity_type, array($eid));
      $entity = reset($entity);
    }

    list(, , $bundle) = entity_extract_ids($entity_type, $entity);
    $entity->bundle = $bundle;
    $entity->eid = $eid;
    $entity->entity_type = $entity_type;

    // Allow resize/drag/drop of an event if user has proper permissions.
    $entity->editable = _fullcalendar_update_access($entity);

    // Create a string of class names.
    $classes = module_invoke_all('fullcalendar_classes', $entity);
    drupal_alter('fullcalendar_classes', $classes, $entity);
    $entity->class = implode(' ', array_unique($classes));

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
    // Fetch custom dates if needed.
    if (!empty($options['date'])) {
      $date_fields = array_intersect_key($date_fields, $options['date_field']);
    }
    $event = array();
    foreach ($date_fields as $field) {
      $instance = field_info_instance($entity_type, $field['field_name'], $bundle);
      $field_data = field_info_field($field['field_name']);

      // Filter fields without value.
      if (!empty($field['value'])) {
        foreach ($field['value'] as $index => $item) {
          $dates = _fullcalendar_get_display_times($entity_type, $entity, $instance, $field_data, $item);
          $event[] = array(
            '#theme' => 'link',
            '#text' => $item['rendered']['#markup'],
            '#path' => $entity->url,
            '#options' => array(
              'attributes' => $dates + array(
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

/**
 * Translates times to the right display times.
 *
 * @see date_formatter_process()
 *
 * @param $entity_type
 *   The type of entity that will be updated.
 * @param $entity
 *   The entity that will be updated.
 * @param $instance
 *   A field instance.
 * @param $field_name
 *   The name of the date field.
 * @param $field
 *   The field structure for the date field.
 * @param $item
 *   The field value for the date field.
 * @param $index
 *   The current index of the date field.
 *
 * @return
 *   Array with the formatted start and end time, and editable/all day booleans.
 */
function _fullcalendar_get_display_times($entity_type, $entity, $instance, $field, $item) {
  if (!isset($item['raw']['date_type'])) {
    $date1 = new DateObject($item['raw']['value'], date_default_timezone(), DATE_FORMAT_UNIX);
    $date2 = $date1;
    $all_day = FALSE;
    $editable = FALSE;
  }
  else {
    $date = date_formatter_process($instance['display']['default']['type'], $entity_type, $entity, $field, $instance, LANGUAGE_NONE, $item['raw'], $instance['display']['default']);
    $date1 = $date['value']['local']['object'];
    $date2 = $date['value2']['local']['object'];
    $all_day = date_field_all_day($field, $instance, $date1, $date2);
    $editable = $entity->editable;
  }
  return array(
    'allDay' => $all_day,
    'start' => $date1->format(DATE_FORMAT_DATETIME),
    'end' => $date2->format(DATE_FORMAT_DATETIME),
    'editable' => $editable,
  );
}
