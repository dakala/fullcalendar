<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\views\style\FullCalendar.
 */

namespace Drupal\fullcalendar\Plugin\views\style;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\fullcalendar\Plugin\FullcalendarPluginBag;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * {@inheritdoc}
   */
  protected $usesFields = TRUE;

  /**
   * {@inheritdoc}
   */
  protected $usesGrouping = FALSE;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Stores the FullCalendar plugins used by this style plugin.
   *
   * @var \Drupal\fullcalendar\Plugin\FullcalendarPluginBag
   */
  protected $pluginBag;

  /**
   * {@inheritdoc}
   */
  public function evenEmpty() {
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
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, PluginManagerInterface $fullcalendar_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->pluginBag = new FullcalendarPluginBag($fullcalendar_manager, $this);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.fullcalendar'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    foreach ($this->pluginBag as $plugin) {
      $options += $plugin->defineOptions();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);
    foreach ($this->pluginBag as $plugin) {
      $plugin->buildOptionsForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(&$form, &$form_state) {
    parent::validateOptionsForm($form, $form_state);

    // Cast all submitted values to their proper type.
    if (!empty($form_state['values']['style_options']) && is_array($form_state['values']['style_options'])) {
      $this->castNestedValues($form_state['values']['style_options'], $form);
    }
  }

  /**
   * Casts form values to a given type, if defined.
   *
   * @param array $values
   *   An array of fullcalendar option values.
   * @param array $form
   *   The fullcalendar option form definition.
   * @param string|null $current_key
   *   (optional) The current key being processed. Defaults to NULL.
   * @param array $parents
   *   (optional) An array of parent keys when recursing through the nested
   *   array. Defaults to an empty array.
   */
  protected function castNestedValues(array &$values, array $form, $current_key = NULL, array $parents = array()) {
    foreach ($values as $key => &$value) {
      // We are leaving a recursive loop, remove the last parent key.
      if (empty($current_key)) {
        array_pop($parents);
      }

      // In case we recurse into an array, or need to specify the key for
      // drupal_array_get_nested_value(), add the current key to $parents.
      $parents[] = $key;

      if (is_array($value)) {
        // Enter another recursive loop.
        $this->castNestedValues($value, $form, $key, $parents);
      }
      else {
        // Get the form definition for this key.
        $form_value = NestedArray::getValue($form, $parents);
        // Check to see if #data_type is specified, if so, cast the value.
        if (isset($form_value['#data_type'])) {
          settype($value, $form_value['#data_type']);
        }
        // Remove the current key from $parents to move on to the next key.
        array_pop($parents);
      }
    }
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function validate() {
    if ($this->displayHandler->display['display_plugin'] != 'default' && !$this->parseFields()) {
      drupal_set_message(t('Display "@display" requires at least one date field.', array('@display' => $this->displayHandler->display['display_title'])), 'error');
    }
    return parent::validate();
  }

  /**
   * {@inheritdoc}
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
      $definition = $plugin->getPluginDefinition();
      foreach (array('css', 'js') as $type) {
        if ($definition[$type]) {
          $attached[$type][] = drupal_get_path('module', $definition['module']) . "/$type/$plugin_id.fullcalendar.$type";
        }
      }
    }
    if ($this->displayHandler->getOption('use_ajax')) {
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
      $definition = $plugin->getPluginDefinition();
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
    foreach ($this->view->result as $delta => $row) {
      // Collect all fields for the customize options.
      $fields = array();
      // Collect only date fields.
      $date_fields = array();
      foreach ($this->view->field as $field_name => $field) {
        $fields[$field_name] = $this->getField($delta, $field_name);
        if (fullcalendar_field_is_date($field)) {
          $date_fields[$field_name] = array(
            'value' => $field->getItems($row),
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
      $classes = $this->moduleHandler->invokeAll('fullcalendar_classes', array($entity));
      $this->moduleHandler->alter('fullcalendar_classes', $classes, $entity);
      $classes = array_map('drupal_html_class', $classes);
      $class = implode(' ', array_unique($classes));

      $event = array();
      foreach ($date_fields as $field) {
        // Filter fields without value.
        if (empty($field['value'])) {
          continue;
        }
        foreach ($field['value'] as $index => $item) {
          $start = $item['raw']['value'];
          $end = $start;
          $all_day = FALSE;
          $uri = $entity->uri();
          $event[] = array(
            '#type' => 'link',
            '#title' => $item['raw']['value'],
            '#href' => $uri['path'],
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
