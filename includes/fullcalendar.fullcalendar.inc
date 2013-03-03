<?php

/**
 * @file
 * Provides default FullCalendar configuration options.
 */

/**
 * Implements hook_fullcalendar_options_submit().
 */
function fullcalendar_fullcalendar_options_submit($form, &$form_state, $view) {
  $options = &$form_state['values']['style_options'];

  // These field options have empty defaults, make sure they stay that way.
  foreach (array('title', 'url', 'date') as $field) {
    if (empty($options['fields'][$field])) {
      unset($options['fields'][$field . '_field']);
    }
  }
}

/**
 * Implements hook_fullcalendar_options_pre_view().
 */
function fullcalendar_fullcalendar_options_pre_view(&$settings, &$view) {
  global $language;

  if (!empty($settings['editable'])) {
    $view->fullcalendar_disallow_editable = TRUE;
  }

  $options = array(
    'buttonText' => array(
      'day' => t('Day'),
      'week' => t('Week'),
      'month' => t('Month'),
      'today' => t('Today'),
    ),
    'allDayText' => t('All day'),
    'monthNames' => array_values(date_month_names(TRUE)),
    'monthNamesShort' => array_values(date_month_names_abbr(TRUE)),
    'dayNames' => date_week_days(TRUE),
    'dayNamesShort' => date_week_days_abbr(TRUE),
    'isRTL' => (bool) $language->direction,
  );
  $advanced = !empty($settings['advanced']);
  foreach ($settings as $key => $value) {
    if (is_array($value)) {
      if ($key == 'times') {
        $settings[$key]['date']['date'] = $value['date']['day'];
        unset($settings[$key]['date']['day']);
      }
      continue;
    }
    elseif (in_array($key, array('left', 'center', 'right'))) {
      $options['header'][$key] = $value;
    }
    elseif (in_array($key, array('timeformatMonth', 'timeformatWeek', 'timeformatDay'))) {
      if ($advanced) {
        $options['timeFormat'][strtolower(substr($key, 10))] = $value;
      }
    }
    elseif (in_array($key, array('columnformatMonth', 'columnformatWeek', 'columnformatDay'))) {
      if ($advanced) {
        $options['columnFormat'][strtolower(substr($key, 12))] = $value;
      }
    }
    elseif (in_array($key, array('titleformatMonth', 'titleformatWeek', 'titleformatDay'))) {
      if ($advanced) {
        $options['titleFormat'][strtolower(substr($key, 11))] = $value;
      }
    }
    elseif ($advanced && $key == 'axisFormat') {
      $options[$key] = $value;
    }
    elseif ($key == 'timeformat') {
      if (!$advanced) {
        $options['timeFormat'] = $value;
      }
    }
    elseif ($key == 'contentHeight' && empty($value)) {
      // Don't add this if it is 0.
    }
    elseif ($key == 'advanced') {
      // Don't add this value ever.
    }
    elseif ($key == 'sameWindow') {
      // Keep this at the top level.
      continue;
    }
    else {
      $options[$key] = $value;
    }
    // Unset all values that have been migrated.
    unset($settings[$key]);
  }
  $settings['fullcalendar'] = $options;

  // First, use the default date if set.
  if (!empty($settings['times']['default_date'])) {
    $settings['date'] = $settings['times']['date'];
  }
  // Unset times settings.
  unset($settings['times']);

  // Get the values from the URL.
  extract($view->get_exposed_input(), EXTR_SKIP);
  if (isset($year) && is_numeric($year)) {
    $settings['date']['year'] = $year;
  }
  if (isset($month) && is_numeric($month) && $month > 0 && $month <= 12) {
    $settings['date']['month'] = $month;
  }
  if (isset($day) && is_numeric($day) && $day > 0 && $day <= 31) {
    $settings['date']['date'] = $day;
  }
  if (isset($mode) && in_array($mode, array('month', 'basicWeek', 'basicDay', 'agendaWeek', 'agendaDay'))) {
    $settings['date']['defaultView'] = $mode;
  }

  // Ensure that some value is set.
  if (!isset($settings['date']['year'])) {
    $settings['date']['year'] = date('Y', strtotime('now'));
  }
  if (!isset($settings['date']['month'])) {
    $settings['date']['month'] = date('n', strtotime('now'));
  }
  // Change month to zero-based.
  $settings['date']['month']--;
}
