<?php

namespace Drupal\fullcalendar\Plugin\views\style;

use DateTime;
use DateTimeZone;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fullcalendar\Plugin\FullcalendarBase;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\fullcalendar\Plugin\FullcalendarPluginCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Html;

/**
 * @todo.
 *
 * @ViewsStyle(
 *   id = "fullcalendar",
 *   title = @Translation("FullCalendar"),
 *   help = @Translation("Displays items on a calendar."),
 *   theme = "fullcalendar",
 *   theme_file = "fullcalendar.theme.inc",
 *   display_types = {"normal"}
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
   * Entity Field Manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * Stores the FullCalendar plugins used by this style plugin.
   *
   * @var \Drupal\fullcalendar\Plugin\FullcalendarPluginCollection
   */
  protected $pluginBag;

  /**
   * @var \Drupal\Core\Datetime\DateFormatter $dateFormatter
   *   The date formatter service.
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public function evenEmpty() {
    return TRUE;
  }

  /**
   * TODO
   *
   * @return \Drupal\fullcalendar\Plugin\FullcalendarPluginCollection|\Drupal\fullcalendar\Plugin\FullcalendarInterface[]
   */
  public function getPlugins() {
    return $this->pluginBag;
  }

  /**
   * Constructs a new Fullcalendar object.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Component\Plugin\PluginManagerInterface $fullcalendar_manager
   *   FullCalendar Manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager
   *   Entity Field Manager.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PluginManagerInterface $fullcalendar_manager, ModuleHandlerInterface $module_handler, $field_manager, DateFormatter $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->pluginBag = new FullcalendarPluginCollection($fullcalendar_manager, $this);
    $this->moduleHandler = $module_handler;
    $this->fieldManager = $field_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.fullcalendar'),
      $container->get('module_handler'),
      $container->get('entity_field.manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    /* @var \Drupal\fullcalendar\Plugin\fullcalendar\type\FullCalendar $plugin */
    foreach ($this->getPlugins() as $plugin) {
      if ($plugin instanceof FullcalendarBase) {
        $options += $plugin->defineOptions();
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    /* @var \Drupal\fullcalendar\Plugin\fullcalendar\type\FullCalendar $plugin */
    foreach ($this->getPlugins() as $plugin) {
      if ($plugin instanceof FullcalendarBase) {
        $plugin->buildOptionsForm($form, $form_state);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) {
    parent::validateOptionsForm($form, $form_state);

    // Cast all submitted values to their proper type.
    // TODO Remove once https://drupal.org/node/1653026 is in.
    if ($form_state->getValue('style_options')) {
      $this->castNestedValues($form_state->getValue('style_options'), $form);
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
  protected function castNestedValues(array &$values, array $form, $current_key = NULL, array $parents = []) {
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
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    parent::submitOptionsForm($form, $form_state);

    /* @var \Drupal\fullcalendar\Plugin\fullcalendar\type\FullCalendar $plugin */
    foreach ($this->getPlugins() as $plugin) {
      if ($plugin instanceof FullcalendarBase) {
        $plugin->submitOptionsForm($form, $form_state);
      }
    }
  }

  /**
   * Extracts date fields from the view.
   */
  public function parseFields($include_gcal = TRUE) {
    $this->view->initHandlers();
    $labels = $this->displayHandler->getFieldLabels();

    $date_fields = [];

    /** @var \Drupal\views\Plugin\views\field\EntityField $field */
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
      drupal_set_message($this->t('Display "@display" requires at least one date field.', [
        '@display' => $this->displayHandler->display['display_title'],
      ]), 'error');
    }

    return parent::validate();
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $this->options['#attached'] = $this->prepareAttached();

    return [
      '#theme'   => $this->themeFunctions(),
      '#view'    => $this->view,
      '#rows'    => $this->prepareEvents(),
      '#options' => $this->options,
    ];
  }

  /**
   * Load libraries.
   */
  protected function prepareAttached() {
    /* @var \Drupal\fullcalendar\Plugin\fullcalendar\type\FullCalendar $plugin */
    $attached['attach']['library'][] = 'fullcalendar/drupal.fullcalendar';

    foreach ($this->getPlugins() as $plugin_id => $plugin) {
      $definition = $plugin->getPluginDefinition();

      foreach (['css', 'js'] as $type) {
        if ($definition[$type]) {
          $attached['attach']['library'][] = $definition['provider'] . '/drupal.' . $plugin_id . '.' . $type;
        }
      }
    }

    if ($this->displayHandler->getOption('use_ajax')) {
      $attached['attach']['library'][] = 'fullcalendar/drupal.fullcalendar.ajax';
    }

    $settings = $this->prepareSettings();

    $attached['attach']['drupalSettings']['fullcalendar'] = [
      '.js-view-dom-id-' . $this->view->dom_id => $settings,
    ];

    if (!empty($settings['fullcalendar']['modalWindow'])) {
      // FIXME all of these libraries are needed?
      $attached['attach']['library'][] = 'core/drupal.ajax';
      $attached['attach']['library'][] = 'core/drupal.dialog';
      $attached['attach']['library'][] = 'core/drupal.dialog.ajax';
    }

    return $attached['attach'];
  }

  /**
   * Prepare JavaScript settings.
   */
  protected function prepareSettings() {
    $settings = &drupal_static(__METHOD__, []);

    if (empty($settings)) {
      $weights = [];
      $delta = 0;

      /* @var \Drupal\fullcalendar\Plugin\fullcalendar\type\FullCalendar $plugin */
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
      // TODO
      $settings['fullcalendar']['disableResizing'] = TRUE;

      // Force to disable dates in the previous or next month in order to get
      // the (real) first and last day of the current month after using pager in
      // 'month' view. So, disabling this results a valid date-range for the
      // current month, instead of the date-range +/- days from the previous and
      // next month. It's very important, because we set default date-range in
      // the same way in fullcalendar_views_pre_view().
      // @see https://fullcalendar.io/docs/display/showNonCurrentDates/
      $settings['fullcalendar']['showNonCurrentDates'] = FALSE;
      $settings['fullcalendar']['fixedWeekCount'] = FALSE;

      // Need to reverse this value.
      if (empty($settings['fullcalendar']['editable'])) {
        $settings['fullcalendar']['editable'] = TRUE;
      }
      else {
        $settings['fullcalendar']['editable'] = FALSE;
      }

      $settings['fullcalendar']['locale'] = \Drupal::languageManager()
        ->getCurrentLanguage()
        ->getId();
    }

    return $settings;
  }

  /**
   * Prepare events for calendar.
   */
  protected function prepareEvents() {
    $events = [];

    foreach ($this->view->result as $delta => $row) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      $entity = $row->_entity;

      // Collect all fields for the customize options.
      $fields = [];
      // Collect only date fields.
      $date_fields = [];
      // Collect prepared events.
      $event = [];

      /* @var \Drupal\views\Plugin\views\field\Field $field */
      foreach ($this->view->field as $field_name => $field) {
        $fields[$field_name] = $this->getField($delta, $field_name);

        if (fullcalendar_field_is_date($field)) {
          $field_storage_definitions = $this->fieldManager->getFieldStorageDefinitions($field->definition['entity_type']);
          $field_definition = $field_storage_definitions[$field->definition['field_name']];

          $date_fields[$field_name] = [
            'value'       => $field->getItems($row),
            'field_alias' => $field->field_alias,
            'field_name'  => $field_definition->getName(),
            'field_info'  => $field_definition,
            'timezone_override'  => $field->options['settings']['timezone_override'],
          ];
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

      foreach ($date_fields as $field) {
        // Filter fields without value.
        if (empty($field['value'])) {
          continue;
        }

        /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface $field_definition */
        $field_definition = $field['field_info'];
        // Get 'min' and 'max' dates appear in the Calendar.
        $date_range = $this->getExposedDates($field['field_name']);

        // "date_recur" field (with recurring date).
        if ($field_definition->getType() == 'date_recur') {
          /** @var \Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList $field_items */
          $field_items = $row->_entity->{$field['field_name']};

          $isRecurring = FALSE;

          /** @var \Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem $item */
          foreach ($field_items as $index => $item) {
            // Get DateRecur Occurrence Handler.
            $occurrenceHandler = $item->getOccurrenceHandler();

            // If this field is a DateRecur field.
            if ($occurrenceHandler->isRecurring()) {
              // Get a list of occurrences for display.
              $occurrences = $occurrenceHandler->getOccurrencesForDisplay($date_range['min'], $date_range['max']);

              foreach ($occurrences as $occurrence) {
                /** @var \DateTime $start */
                $start = $occurrence['value'];
                /** @var \DateTime $end */
                $end = $occurrence['end_value'];

                $event[] = $this->prepareEvent($entity, $field, $index, $start, $end);
              }

              $isRecurring = TRUE;
            }
          }

          if ($isRecurring === TRUE) {
            // At this point, all DateRecur occurrences are merged into $rows
            // so we can continue adding date items with the next field.
            continue;
          }
        }

        // "datetime" and "daterange" fields or "date_recur" field (without
        // recurring date).
        foreach ($field['value'] as $index => $item) {
          // Start time is required!
          if (empty($item['raw']->value)) {
            continue;
          }

          $event_start = new DateTime();
          $event_start->setTimestamp(strtotime($item['raw']->value));

          // Set event timezone override if configured in views.
          if (!empty($field['timezone_override'])) {
            $event_start->setTimeZone(new DateTimeZone($field['timezone_override']));
          }

          $event_end = new DateTime();
          // By default, we use the start-time + 1 hour as end-time.
          $event_end->setTimestamp($event_start->getTimestamp() + 3600);

          // If the field has end_value, override default end-time.
          if (!empty($item['raw']->end_value)) {
            $event_end->setTimestamp(strtotime($item['raw']->end_value));

            // Set event timezone override if configured in views.
            if (!empty($field['timezone_override'])) {
              $event_end->setTimeZone(new DateTimeZone($field['timezone_override']));
            }
          }

          $event_start->setTimestamp($event_start->getTimestamp() + $event_start->getOffset());
          $event_end->setTimestamp($event_end->getTimestamp() + $event_end->getOffset());

          $event[] = $this->prepareEvent($entity, $field, $index, $event_start, $event_end);
        }
      }

      if (!empty($event)) {
        $events[$delta] = [
          '#theme'  => 'fullcalendar_event',
          '#event'  => $event,
          '#entity' => $entity,
        ];
      }
    }

    return $events;
  }

  /**
   * Helper method to prepare event.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Event entity.
   * @param $field
   *
   * @param int $delta
   *   Field delta.
   * @param \DateTime $event_start
   *   Start date of the event.
   * @param \DateTime $event_end
   *   End date of the event.
   *
   * @return array
   */
  private function prepareEvent($entity, $field, $delta, $event_start, $event_end) {
    $classes = $this->moduleHandler->invokeAll('fullcalendar_classes', [$entity]);
    $this->moduleHandler->alter('fullcalendar_classes', $classes, $entity);

    $classes = array_map([
      '\Drupal\Component\Utility\Html',
      'getClass'
    ], $classes);
    $class = (count($classes)) ? implode(' ', array_unique($classes)) : '';

    $request_time = \Drupal::time()->getRequestTime();
    $current_time = new DateTime();
    $current_time->setTimestamp($request_time);

    // Get 'min' and 'max' dates appear in the Calendar.
    $date_range = $this->getExposedDates($field['field_name']);

    // All-day option (for agenda views) is FALSE by default.
    $all_day = FALSE;

    // If the event starts before and ends after the current date-range
    // appears in the Calendar, set event to all-day event.
    if ($event_start <= $date_range['min'] && $event_end >= $date_range['max']) {
      $all_day = TRUE;
    }

    // Add a class if the event was in the past or is in the future, based
    // on the end time. We can't do this in hook_fullcalendar_classes()
    // because the date hasn't been processed yet.
    if (($all_day && $event_start < $current_time) || (!$all_day && $event_end < $current_time)) {
      $time_class = 'fc-event-past';
    }
    elseif ($event_start > $current_time) {
      $time_class = 'fc-event-future';
    }
    else {
      $time_class = 'fc-event-now';
    }

    $settings = $this->prepareSettings();

    if (!empty($settings['fullcalendar']['editable'])) {
      $editable = $entity->access('update', NULL, TRUE)->isAllowed();
    }
    else {
      $editable = FALSE;
    }

    $url = $entity->toUrl('canonical', [
      'language' => \Drupal::languageManager()->getCurrentLanguage(),
    ]);

    $url->setOption('attributes', [
      'data-all-day'     => (int) $all_day,
      'data-start'       => $this->dateFormatter->format($event_start->getTimestamp(), 'custom', DATETIME_DATETIME_STORAGE_FORMAT),
      'data-end'         => $this->dateFormatter->format($event_end->getTimestamp(), 'custom', DATETIME_DATETIME_STORAGE_FORMAT),
      'data-editable'    => !empty($editable) ? 'true' : 'false',
      // (int) $editable doesn't work...
      'data-field'       => $field['field_name'],
      'data-index'       => $delta,
      'data-eid'         => $entity->id(),
      'data-entity-type' => $entity->getEntityTypeId(),
      'data-cn'          => $class . ' ' . $time_class,
      'title'            => strip_tags(htmlspecialchars_decode($entity->label(), ENT_QUOTES)),
      'class'            => [
        'fullcalendar-event-details',
      ],
    ]);

    return $url->toRenderArray() + [
      '#type'  => 'link',
      '#title' => $entity->label(),
      '#cache' => [
        'contexts' => [
          'user', // Because of 'data-editable'.
        ],
      ],
    ];
  }

  /**
   * Get 'min' and 'max' dates appear in the Calendar.
   *
   * @param $field_name
   *   Field machine name.
   *
   * @return array
   */
  public function getExposedDates($field_name) {
    $dates = &drupal_static(__METHOD__, []);

    if (empty($dates[$field_name])) {
      $entity_type = $this->view->getBaseEntityType();
      $entity_type_id = $entity_type->id();

      $settings = $this->view->style_plugin->options;

      /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
      $field_manager = \Drupal::getContainer()->get('entity_field.manager');
      /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface[] $field_storages */
      $field_storages = $field_manager->getFieldStorageDefinitions($entity_type_id);
      /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage */
      $field_storage = $field_storages[$field_name];
      $field_value = $field_storage->getName() . '_value';

      $exposed_input = $this->view->getExposedInput();

      // Min and Max dates for exposed filter.
      $dateMin = new DateTime();
      $dateMax = new DateTime();

      // First, we try to set initial Min and Max date values based on the
      // exposed form values.
      if (isset($exposed_input[$field_value])) {
        $dateMin->setTimestamp(strtotime($exposed_input[$field_value]['min']));
        $dateMax->setTimestamp(strtotime($exposed_input[$field_value]['max']));
      }
      // If no exposed values set, use user-defined date values.
      elseif (!empty($settings['date']['month']) && !empty($settings['date']['year'])) {
        $ts = mktime(0, 0, 0, $settings['date']['month'] + 1, 1, $settings['date']['year']);

        $dateMin->setTimestamp($ts);
        $dateMax->setTimestamp($ts);

        $dateMin->modify('first day of this month');
        $dateMax->modify('first day of next month');
      }
      // Use default 1 month date-range.
      else {
        $dateMin->modify('first day of this month');
        $dateMax->modify('first day of next month');
      }

      $dates[$field_name] = [
        'min' => $dateMin,
        'max' => $dateMax,
      ];
    }

    return $dates[$field_name];
  }

}
