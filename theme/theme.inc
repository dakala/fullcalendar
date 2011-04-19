<?php

/**
 * Passes options to the FullCalendar plugin as JavaScript settings.
 */
function template_preprocess_fullcalendar(&$vars) {
  $view = $vars['view'];
  $options = $vars['options'];

  static $dom_id = 1;
  if (empty($view->dom_id)) {
    $view->dom_id = 'fc-' . $dom_id++;
  }
  $options['dom_id'] = '.view-dom-id-' . $view->dom_id;

  $options['gcal'] = array();
  foreach ($view->field as $field) {
    if ($field->field == 'gcal') {
      $options['gcal'][] = $field->render(array());
    }
  }

  fullcalendar_get_settings($options);
}

/**
 * Prepares variables for template file invoked for node row type.
 */
function template_preprocess_fullcalendar_fields(&$vars) {
  // Check if we have field data.
  if (empty($vars['row'])) {
    return;
  }

  foreach ($vars['view']->field as $id => $field) {
    $fields[$id] = $vars['view']->style_plugin->get_field($vars['view']->row_index, $id);
    if (method_exists($field, 'fullcalendar_render')) {
      $date_fields[$id] = $field->fullcalendar_render($vars['row']);
    }
  }

  $vars['data'] = array(); // Contains the start, end & allDay values.

  $eid = $vars['row']->{$vars['view']->base_field};
  if (isset($vars['row']->_field_data)) {
    $entity = $vars['row']->_field_data[$vars['view']->base_field]['entity'];
    $entity_type = $vars['row']->_field_data[$vars['view']->base_field]['entity_type'];
  }
  else {
    foreach (entity_get_info() as $entity_type => $entity_info) {
      if ($entity_info['base table'] == $vars['view']->base_table) {
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

  $entity->editable = _fullcalendar_update_access($entity);

  // Create a string of class names
  $classes = module_invoke_all('fullcalendar_classes', $entity);
  drupal_alter('fullcalendar_classes', $classes, $entity);
  $entity->class = implode(' ', array_unique($classes));

  // Default URL.
  $uri = entity_uri($entity_type, $entity);
  $entity->url = $uri['path'];
  // Fetch custom URL if needed.
  if (!empty($vars['options']['custom']['fc_url'])) {
    $field_name = $vars['options']['custom']['fc_url_field'];
    if (!empty($fields[$field_name])) {
      $entity->url = ltrim($fields[$field_name], '/');
    }
  }

  // Fetch custom title if needed.
  if (!isset($entity->title)) {
    $entity->title = '';
  }
  if (!empty($vars['options']['custom']['fc_title'])) {
    $field_name = $vars['options']['custom']['fc_title_field'];
    if (!empty($fields[$field_name])) {
      $entity->title = $fields[$field_name];
    }
  }

  // Entity is build, add it to the preprocess variables
  $vars['entity'] = $entity;

  // Fetch custom dates if needed.
  if (!empty($vars['options']['custom']['fc_date'])) {
    $date_fields = array_intersect_key($date_fields, $vars['options']['custom']['fc_date_field']);
  }

  // Iterate through all available fields.
  foreach ($date_fields as $field) {
    $instance = field_info_instance($entity_type, $field['field_name'], $bundle);
    $field_data = field_info_field($field['field_name']);
    // Filter fields without value.
    if (!empty($field['value'])) {
      foreach ($field['value'] as $index => $item) {
        $vars['data'][] = _fullcalendar_set_display_times($entity_type, $entity, $instance, $field_data, $item, $index);
      }
    }
  }
}
