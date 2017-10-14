<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_legend\Plugin\Block\FullcalendarLegendBase.
 */

namespace Drupal\fullcalendar_legend\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;

/**
 * Provides a generic FullCalendar Legend block.
 */
abstract class FullcalendarLegendBase extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $view_id = \Drupal::routeMatch()->getParameter('view_id');
    $view = Views::getView($view_id);

    if (empty($view)) {
      return NULL;
    }

    $style = $view->display_handler->getOption('style');
    if ($style['type'] != 'fullcalendar') {
      return NULL;
    }

    $fields = [];

    $fieldManager = \Drupal::getContainer()->get('entity_field.manager');

    /** @var \Drupal\views\Plugin\views\field\EntityField $field */
    foreach ($view->field as $field_name => $field) {
      if (fullcalendar_field_is_date($field)) {
        $field_storage_definitions = $fieldManager->getFieldStorageDefinitions($field->definition['entity_type']);
        $field_definition = $field_storage_definitions[$field->definition['field_name']];

        $fields[$field_name] = $field_definition;
      }
    }

    return [
      '#theme' => 'fullcalendar_legend',
      '#types' => $this->buildLegend($fields),
    ];
  }

  /**
   * @param \Drupal\Core\Field\FieldDefinitionInterface[] $fields
   *
   * @return array
   */
  abstract protected function buildLegend(array $fields);

}
