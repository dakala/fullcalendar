<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\views\style\FullCalendar.
 */

namespace Drupal\fullcalendar\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\fullcalendar\Plugin\FullcalendarPluginBag;

/**
 * @todo.
 *
 * @Plugin(
 *   id = "fullcalendar",
 *   title = @Translation("FullCalendar"),
 *   help = @Translation("Displays items on a calendar."),
 *   module = "fullcalendar",
 *   theme = "fullcalendar",
 *   theme_file = "fullcalendar.theme.inc",
 *   type = "normal"
 * )
 */
class FullCalendar extends StylePluginBase {

  /**
   * @todo.
   */
  protected $usesFields = TRUE;

  /**
   * @todo.
   */
  protected $usesGrouping = FALSE;

  /**
   * @todo
   *
   * @var \Drupal\fullcalendar\Plugin\FullcalendarPluginBag
   */
  protected $pluginBag;

  /**
   * @todo.
   */
  public function even_empty() {
    return TRUE;
  }

  /**
   * @todo.
   */
  public function getPlugins() {
    return $this->pluginBag;
  }

  /**
   * @todo.
   */
  public function __construct(array $configuration, $plugin_id, DiscoveryInterface $discovery) {
    parent::__construct($configuration, $plugin_id, $discovery);

    $this->pluginBag = new FullcalendarPluginBag(drupal_container()->get('plugin.manager.fullcalendar'), $this);
  }

  /**
   * @todo.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    foreach ($this->pluginBag as $plugin) {
      $options += $plugin->defineOptions();
    }
    return $options;
  }

  /**
   * @todo.
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);
    foreach ($this->pluginBag as $plugin) {
      $plugin->buildOptionsForm($form, $form_state);
    }
  }

  /**
   * @todo.
   */
  public function submitOptionsForm(&$form, &$form_state) {
    parent::submitOptionsForm($form, $form_state);
    foreach ($this->pluginBag as $plugin) {
      $plugin->submitOptionsForm($form, $form_state);
    }
  }

  /**
   * @todo.
   */
  public function parseFields($include_gcal = TRUE) {
    $this->view->initHandlers();
    $labels = $this->displayHandler->getFieldLabels();
    $date_fields = array();
    foreach ($this->view->field as $id => $field) {
      if (fullcalendar_field_is_date($field, $include_gcal)) {
        $date_fields[$id] = $labels[$id];
      }
    }
    return $date_fields;
  }

  /**
   * @todo.
   */
  public function validate() {
    if ($this->displayHandler->display['display_plugin'] != 'default' && !$this->parseFields()) {
      drupal_set_message(t('Display "@display" requires at least one date field.', array('@display' => $this->displayHandler->display['display_title'])), 'error');
    }
    return parent::validate();
  }

  public function render() {
    return array(
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#rows' => $this->prepareEvents($this->view->result, $this->options['fields']),
      '#options' => $this->options,
    );
  }

  protected function prepareEvents($rows, $options) {
    if (empty($rows)) {
      return;
    }

    $events = array();
    foreach ($rows as $delta => $row) {
      // Collect all fields for the customize options.
      $fields = array();
      // Collect only date fields.
      $date_fields = array();
      foreach ($this->view->field as $field_name => $field) {
        $fields[$field_name] = $this->get_field($delta, $field_name);
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
                  'editable' => (int) TRUE,//$entity->editable,
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

}
