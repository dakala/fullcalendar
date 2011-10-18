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
 * Process the view and add the relevant CSS and JS.
 *
 * @param $view
 *   The view object.
 */
function fullcalendar_get_settings($view) {
  // If this style plugin isn't ours, return.
  if ($view->display_handler->get_option('style_plugin') != 'fullcalendar') {
    return;
  }

  // Bail out if we're getting results from ajax callback.
  if (isset($view->fullcalendar_ajax)) {
    return;
  }

  global $language;

  $options = $view->style_plugin->options;
  static $fc_dom_id = 1;
  if (empty($view->dom_id)) {
    $view->dom_id = 'fc-' . $fc_dom_id++;
  }
  $dom_id = '.view-dom-id-' . $view->dom_id;

  $options['gcal'] = array();
  foreach ($view->field as $field) {
    if ($field->field == 'gcal') {
      $options['gcal'][] = $field->render(array());
    }
  }

  // Remove times from $options so they can be set conditionally later.
  $times = $options['times'];
  unset($options['times']);
  unset($options['fields']);

  $settings = array(
    $dom_id => $options + array(
      'monthNames' => array_values(date_month_names(TRUE)),
      'monthNamesShort' => array_values(date_month_names_abbr(TRUE)),
      'dayNames' => date_week_days(TRUE),
      'dayNamesShort' => date_week_days_abbr(TRUE),
      'allDayText' => t('All day'),
      'dayString' => t('Day'),
      'weekString' => t('Week'),
      'monthString' => t('Month'),
      'todayString' => t('Today'),
      'isRTL' => $language->direction,
      'view_name' => $view->name,
      'view_display' => $view->current_display,
    ),
  );

  if ($times['default_date']) {
    $settings[$dom_id]['year'] = $times['date']['year'];
    $settings[$dom_id]['month'] = $times['date']['month'];
    $settings[$dom_id]['day'] = $times['date']['day'];
  }

  extract($view->get_exposed_input(), EXTR_SKIP);
  if (isset($year) && is_numeric($year)) {
    $settings[$dom_id]['year'] = $year;
  }
  if (isset($month) && is_numeric($month) && $month > 0 && $month <= 12) {
    $settings[$dom_id]['month'] = $month;
  }
  if (isset($day) && is_numeric($day) && $day > 0 && $day <= 31) {
    $settings[$dom_id]['day'] = $day;
  }
  if (isset($mode) && in_array($mode, array('month', 'basicWeek', 'basicDay', 'agendaWeek', 'agendaDay'))) {
    $settings[$dom_id]['defaultView'] = $mode;
  }

  // Check if there is an argument which is an instance the default
  // date argument class and create month, year variables as well as next
  // and previous urls from all arguments.
  if (!empty($view->argument) && $view->use_ajax) {
    $settings[$dom_id] += fullcalendar_check_arguments($view);
  }

  drupal_add_library('fullcalendar', 'fullcalendar');
  drupal_add_js(array('fullcalendar' => $settings), 'setting');
  ctools_add_css('fullcalendar.theme', 'fullcalendar');
  ctools_add_js('fullcalendar.views', 'fullcalendar');
}

/**
 * Check arguments on a view. If a date handler is found, add more
 * info to the $settings array so we know in jQuery we can fetch
 * new data from the view.
 *
 * @param $view
 *   The current view we're processing.
 *
 * @return $settings
 *   A collection of extra settings.
 */
function fullcalendar_check_arguments($view) {
  $date_handler = NULL;
  $args = array();
  $settings = array('ajax' => FALSE);

  $position = 0;
  foreach ($view->argument as $argument_key => $handler) {
    if (date_views_handler_is_date($handler, 'argument')) {
      $date_handler = $handler;
      $args[] = 'full_calendar_browse';
    }
    else {
      $args[] = $view->args[$position];
    }
    $position++;
  }

  if ($date_handler) {
    $timestamp = strtotime($date_handler->argument);
    $separator = (strpos($date_handler->argument, '-') !== FALSE) ? '-' : '';

    // Add separator and ajax for date argument.
    $settings['args'] = implode('|', $args);
    $settings['separator'] = $separator;
    $settings['ajax'] = TRUE;

    // Settings for FullCalendar.
    $month = date('n', $timestamp);
    $year = date('Y', $timestamp);
    $settings['month'] = $month - 1;
    $settings['year'] = $year;
  }

  return $settings;
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
