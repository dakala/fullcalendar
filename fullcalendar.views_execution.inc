<?php

/**
 * @file
 * Contains Views module runtime hooks.
 */

use Drupal\Component\Utility\Html;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_plugins_alter().
 */
function fullcalendar_views_plugins_alter(&$plugins) {
  $plugins['display']['fullcalendar'] = [
      'title' => t('FullCalendar - Deprecated, use page display instead!'),
      'help'  => t('Stop using this display, use page instead'),
      'no ui' => TRUE,
    ] + $plugins['display']['page'];
}

/**
 * Implements hook_views_pre_view().
 *
 * Add an argument that provides the current date for each date field present.
 */
function fullcalendar_views_pre_view(ViewExecutable $view, $display_id, array $args) {
  $style = $view->display_handler->getOption('style');

  if ($style['type'] != 'fullcalendar') {
    return;
  }

  // Get the current view settings.
  $view->initStyle();
  /** @var \Drupal\fullcalendar\Plugin\views\style\FullCalendar $view_style */
  $view_style = $view->style_plugin;

  $settings = $view->style_plugin->options;

  foreach ($view_style->getPlugins() as $plugin) {
    $plugin->preView($settings);
  }

  $settings['fullcalendar_fields_count'] = 0;
  $exposed_input = $view->getExposedInput();

  /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
  $field_manager = \Drupal::getContainer()->get('entity_field.manager');

  $entity_type = $view->getBaseEntityType();
  $entity_type_id = $entity_type->id();

  /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface[] $field_storages */
  $field_storages = $field_manager->getFieldStorageDefinitions($entity_type_id);

  // Loop through each date field and provide an argument for it.
  foreach ($view->display_handler->getHandlers('field') as $field_id => $field) {
    /** @var \Drupal\views\Plugin\views\field\EntityField $field */
    if (!fullcalendar_field_is_date($field)) {
      continue;
    }

    /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage */
    $field_storage = $field_storages[$field->definition['field_name']];

    // Default table name for the field.
    $field_table = $field_storage->getTargetEntityTypeId() . '__' . $field_storage->getName();

    // If the field is a DateRecur field, need to apply table prefix.
    if ($field_storage->getType() == 'date_recur') {
      $field_table = 'date_recur__' . $field_table;
    }

    $field_value = $field_storage->getName() . '_value';

    // Add an exposed filter for the date field.
    if (isset($exposed_input[$field_value])) {
      $min = date('Y-m-d', strtotime($exposed_input[$field_value]['min']));
      $max = date('Y-m-d', strtotime($exposed_input[$field_value]['max']));
    }
    else {
      $min = date('Y-m-d', mktime(0, 0, 0, $settings['date']['month'] + 1, 1, $settings['date']['year']));
      $max = date('Y-m-d', mktime(0, 0, 0, $settings['date']['month'] + 2, 1, $settings['date']['year']));
    }

    $options = [
      'exposed'   => TRUE,
      'form_type' => 'date_select',
      'operator'  => 'between',
      'value'     => [
        'type' => 'date',
        'min'  => $min,
        'max'  => $max,
      ],
      'group'     => 0,
    ];

    if (!empty($field->options['relationship'])) {
      $options['relationship'] = $field->options['relationship'];
    }

    $option_id = $view->addHandler($display_id, 'filter', $field_table, $field_value, $options);

    $settings['fullcalendar_fields'][$option_id] = Html::getClass($option_id);
    $settings['fullcalendar_fields_count']++;

    $view->setHandlerOption($display_id, 'filter', $option_id, 'expose', [
      'identifier' => $option_id,
      'operator'   => $option_id . '_op',
    ]);
  }

  // Needs for JavaScript.
  $settings['ajax'] = $view->display_handler->getOption('use_ajax');

  $view->style_plugin->options = $settings;
}

/**
 * Implements hook_views_query_alter().
 */
function fullcalendar_views_query_alter($view, $query) {
  $style = $view->display_handler->getOption('style');

  if ($style['type'] != 'fullcalendar') {
    return;
  }

  // Force the query to be distinct.
  $query->distinct = TRUE;
}

/**
 * Implements hook_views_ajax_data_alter().
 */
function fullcalendar_views_ajax_data_alter(&$commands, &$view) {
  $style = $view->display_handler->getOption('style');

  if ($style['type'] != 'fullcalendar') {
    $commands = [];
  }
}
