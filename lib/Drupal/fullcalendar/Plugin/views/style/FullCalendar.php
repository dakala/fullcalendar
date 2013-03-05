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
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::$usesFields.
   */
  protected $usesFields = TRUE;

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::$usesGrouping.
   */
  protected $usesGrouping = FALSE;

  /**
   * Stores the FullCalendar plugins used by this style plugin.
   *
   * @var \Drupal\fullcalendar\Plugin\FullcalendarPluginBag
   */
  protected $pluginBag;

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::even_empty().
   */
  public function even_empty() {
    return TRUE;
  }

  /**
   * @todo.
   *
   * @return \Drupal\fullcalendar\Plugin\FullcalendarPluginBag
   */
  public function getPlugins() {
    return $this->pluginBag;
  }

  /**
   * Constructs a new Fullcalendar object.
   */
  public function __construct(array $configuration, $plugin_id, DiscoveryInterface $discovery) {
    parent::__construct($configuration, $plugin_id, $discovery);

    $this->pluginBag = new FullcalendarPluginBag(drupal_container()->get('plugin.manager.fullcalendar'), $this);
  }

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::defineOptions().
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    foreach ($this->pluginBag as $plugin) {
      $options += $plugin->defineOptions();
    }
    return $options;
  }

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::buildOptionsForm().
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);
    foreach ($this->pluginBag as $plugin) {
      $plugin->buildOptionsForm($form, $form_state);
    }
  }

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::submitOptionsForm().
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
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::validate().
   */
  public function validate() {
    if ($this->displayHandler->display['display_plugin'] != 'default' && !$this->parseFields()) {
      drupal_set_message(t('Display "@display" requires at least one date field.', array('@display' => $this->displayHandler->display['display_title'])), 'error');
    }
    return parent::validate();
  }

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::render().
   */
  public function render() {
    if (empty($this->view->fullcalendar_ajax)) {
      $this->options['#attached'] = $this->prepareAttached();
    }
    return array(
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#rows' => $this->prepareEvents(),
      '#options' => $this->options,
    );
  }

  /**
   * @todo.
   */
  protected function prepareAttached() {
    $attached['library'][] = array('fullcalendar', 'fullcalendar-module');
    foreach ($this->getPlugins() as $plugin_id => $plugin) {
      $definition = $plugin->getDefinition();
      foreach (array('css', 'js') as $type) {
        if ($definition[$type]) {
          $attached[$type][] = drupal_get_path('module', $definition['module']) . "/$type/$plugin_id.fullcalendar.$type";
        }
      }
    }
    if ($this->view->display_handler->getOption('use_ajax')) {
      $attached['js'][] = drupal_get_path('module', 'fullcalendar') . '/js/fullcalendar.ajax.js';
    }
    $attached['js'][] = array(
      'type' => 'setting',
      'data' => array(
        'fullcalendar' => array(
          '.view-dom-id-' . $this->view->dom_id => $this->prepareSettings(),
        ),
      ),
    );
    return $attached;
  }

  /**
   * @todo.
   */
  protected function prepareSettings() {
    $settings = array();
    $weights = array();
    $delta = 0;
    foreach ($this->getPlugins() as $plugin_id => $plugin) {
      $definition = $plugin->getDefinition();
      $plugin->process($settings);
      if (isset($definition['weight']) && !isset($weights[$definition['weight']])) {
        $weights[$definition['weight']] = $plugin_id;
      }
      else {
        while (isset($weights[$delta])) {
          $delta++;
        }
        $weights[$delta] = $plugin_id;
      }
    }
    ksort($weights);
    $settings['weights'] = array_values($weights);
    // @todo.
    $settings['fullcalendar']['disableResizing'] = TRUE;
    return $settings;
  }

  /**
   * @todo.
   */
  protected function prepareEvents() {
    $events = array();
    if (empty($this->view->result)) {
      return $events;
    }

    foreach ($this->view->result as $delta => $row) {
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
      if (!empty($this->options['fields']['date'])) {
        $date_fields = array_intersect_key($date_fields, $this->options['fields']['date_field']);
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
        if (empty($field['value'])) {
          continue;
        }
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
              'html' => TRUE,
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
            ),
          );
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
