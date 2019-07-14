<?php

namespace Drupal\fullcalendar\Plugin\fullcalendar\type;

use Drupal\Core\Datetime\DateHelper;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\fullcalendar\Plugin\FullcalendarBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * @todo.
 *
 * @FullcalendarOption(
 *   id = "fullcalendar",
 *   module = "fullcalendar",
 *   js = TRUE,
 *   weight = "-20"
 * )
 */
class FullCalendar extends FullcalendarBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('language_manager')
    );
  }

  /**
   * TODO
   *
   * @var array
   */
  protected static $formats = [
    '12'  => [
      'time'      => 'h:mm',
      'slotLabel' => 'h(:mm)a',
    ],
    '24'  => [
      'time'      => 'HH:mm',
      'slotLabel' => 'HH(:mm)',
    ],
    'mdy' => [
      'title'  => [
        'month' => 'MMMM YYYY',
        'week'  => 'MMM D YYYY',
        'day'   => 'MMMM D YYYY',
      ],
      'column' => [
        'month' => 'ddd',
        'week'  => 'ddd M/D',
        'day'   => 'dddd',
      ],
    ],
    'dmy' => [
      'title'  => [
        'month' => 'MMMM YYYY',
        'week'  => 'D MMM YYYY',
        'day'   => 'D MMMM YYYY',
      ],
      'column' => [
        'month' => 'ddd',
        'week'  => 'ddd D/M',
        'day'   => 'dddd',
      ],
    ],
    'ymd' => [
      'title'  => [
        'month' => 'YYYY MMMM',
        'week'  => 'YYYY MMM D',
        'day'   => 'YYYY MMMM D',
      ],
      'column' => [
        'month' => 'ddd',
        'week'  => 'ddd M/D',
        'day'   => 'dddd',
      ],
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $time = '12';
    $date = 'mdy';

    $time_format = static::$formats[$time];
    $date_format = static::$formats[$date];

    $options = [
      'defaultView'       => [
        'default' => 'month',
      ],
      'firstDay'          => [
        'default' => '0',
      ],
      'weekMode'          => [
        'default' => 'fixed',
      ],
      'left'              => [
        'default' => 'today prev,next',
      ],
      'center'            => [
        'default' => 'title',
      ],
      'right'             => [
        'default' => 'month agendaWeek agendaDay',
      ],
      'timeformat'        => [
        'default' => $time_format['time'],
      ],
      'advanced'          => [
        'default' => FALSE,
      ],
      'slotLabelFormat'   => [
        'default' => $time_format['slotLabel'],
      ],
      'timeformatMonth'   => [
        'default' => $time_format['time'],
      ],
      'titleformatMonth'  => [
        'default' => $date_format['title']['month'],
      ],
      'columnformatMonth' => [
        'default' => $date_format['column']['month'],
      ],
      'timeformatWeek'    => [
        'default' => $time_format['time'],
      ],
      'titleformatWeek'   => [
        'default' => $date_format['title']['week'],
      ],
      'columnformatWeek'  => [
        'default' => $date_format['column']['week'],
      ],
      'timeformatDay'     => [
        'default' => $time_format['time'],
      ],
      'titleformatDay'    => [
        'default' => $date_format['title']['day'],
      ],
      'columnformatDay'   => [
        'default' => $date_format['column']['day'],
      ],
      'theme'             => [
        'default' => TRUE,
      ],
      'sameWindow'        => [
        'default' => FALSE,
      ],
      'modalWindow'       => [
        'default' => FALSE,
      ],
      'contentHeight'     => [
        'default' => 0,
      ],
      'droppable'         => [
        'default' => FALSE,
      ],
      'editable'          => [
        'default' => FALSE,
      ],
    ];

    // Nest these explicitly so that they can be more easily found later.
    $options['times'] = [
      'contains' => [
        'default_date' => [
          'default' => FALSE,
        ],
        'date'         => [
          'default' => [
            'year'  => '1900',
            'month' => '1',
            'day'   => '1',
          ],
        ],
      ],
    ];

    $options['fields'] = [
      'contains' => [
        'title_field' => [
          'default' => 'title',
        ],
        'url_field'   => [
          'default' => 'title',
        ],
        'date_field'  => [
          'default' => [],
        ],
        'title'       => [
          'default' => FALSE,
        ],
        'url'         => [
          'default' => FALSE,
        ],
        'date'        => [
          'default' => FALSE,
        ],
      ],
    ];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function process(&$settings) {
    static $fc_dom_id = 1;

    if (empty($this->style->view->dom_id)) {
      $this->style->view->dom_id = 'fc-' . $fc_dom_id++;
    }

    $options = $this->style->options;
    $options['gcal'] = [];

    /* @var \Drupal\Core\Field\FieldStorageDefinitionInterface $field */
    foreach ($this->style->view->field as $field) {
      if (!empty($field->field) && $field->field == 'gcal') {
        $options['gcal'][] = $field->getSettings();
      }
    }

    unset($options['fields']);

    $settings += $options + [
        'view_name'    => $this->style->view->storage->id(),
        'view_display' => $this->style->view->current_display,
      ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    /** @var \Drupal\fullcalendar\Plugin\views\style\FullCalendar $style_plugin */
    $style_plugin = $this->style;

    $form['display'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Display settings'),
      '#collapsible' => TRUE,
      '#open'        => TRUE,
      '#prefix'      => '<div class="clearfix">',
      '#suffix'      => '</div>',
    ];

    $form['header'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Header settings'),
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/display/header', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#collapsible' => TRUE,
      '#open'        => FALSE,
      '#prefix'      => '<div class="clearfix">',
      '#suffix'      => '</div>',
    ];

    $form['times'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Time/date settings'),
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/utilities/formatDate', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#collapsible' => TRUE,
      '#open'        => FALSE,
      '#prefix'      => '<div class="clearfix">',
      '#suffix'      => '</div>',
    ];

    $form['style'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Style settings'),
      '#collapsible' => TRUE,
      '#open'        => FALSE,
    ];

    $form['display']['defaultView'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Initial display'),
      '#options'       => [
        'month'      => $this->t('Month'),
        'agendaWeek' => $this->t('Week (Agenda)'),
        'basicWeek'  => $this->t('Week (Basic)'),
        'agendaDay'  => $this->t('Day (Agenda)'),
        'basicDay'   => $this->t('Day (Basic)'),
      ],
      '#description'   => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/views/Available_Views', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#default_value' => $style_plugin->options['display']['defaultView'],
      '#prefix'        => '<div class="views-left-30">',
      '#suffix'        => '</div>',
      '#fieldset'      => 'display',
    ];

    $form['display']['firstDay'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Week starts on'),
      '#options'       => DateHelper::weekDays(TRUE),
      '#default_value' => $style_plugin->options['display']['firstDay'],
      '#prefix'        => '<div class="views-left-30">',
      '#suffix'        => '</div>',
      '#fieldset'      => 'display',
    ];

    $form['display']['weekMode'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Week mode'),
      '#options'       => [
        'fixed'    => $this->t('Fixed'),
        'liquid'   => $this->t('Liquid'),
        'variable' => $this->t('Variable'),
      ],
      '#default_value' => $style_plugin->options['display']['weekMode'],
      '#description'   => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/display/weekMode', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#fieldset'      => 'display',
    ];

    $form['left'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Left'),
      '#default_value' => $style_plugin->options['left'],
      '#prefix'        => '<div class="views-left-30">',
      '#suffix'        => '</div>',
      '#size'          => '30',
      '#fieldset'      => 'header',
    ];

    $form['center'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Center'),
      '#default_value' => $style_plugin->options['center'],
      '#prefix'        => '<div class="views-left-30">',
      '#suffix'        => '</div>',
      '#size'          => '30',
      '#fieldset'      => 'header',
    ];

    $form['right'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Right'),
      '#default_value' => $style_plugin->options['right'],
      '#size'          => '30',
      '#fieldset'      => 'header',
    ];

    $form['times']['default_date'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Use a custom initial date'),
      '#description'   => $this->t('If unchecked, the calendar will load the current date.'),
      '#default_value' => $style_plugin->options['times']['default_date'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'times',
    ];

    $form['times']['date'] = [
      '#type'          => 'date',
      '#title'         => $this->t('Custom initial date'),
      '#title_display' => 'invisible',
      '#default_value' => $style_plugin->options['times']['date'],
      '#states'        => [
        'visible' => [
          ':input[name="style_options[times][default_date]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
      '#fieldset'      => 'times',
    ];

    $form['timeformat'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Time format'),
      '#default_value' => $style_plugin->options['timeformat'],
      '#size'          => '30',
      '#fieldset'      => 'times',
      '#states'        => [
        'visible' => [
          ':input[name="style_options[advanced]"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];

    $form['advanced'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Enable advanced time and date format settings'),
      '#default_value' => $style_plugin->options['advanced'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'times',
    ];

    $form['slotLabelFormat'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Slot label format'),
      '#description'   => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/agenda/slotLabelFormat', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#default_value' => $style_plugin->options['slotLabelFormat'],
      '#size'          => '30',
      '#fieldset'      => 'times',
      '#states'        => [
        'visible' => [
          ':input[name="style_options[advanced]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];

    // Add the nine time/date formats.
    foreach (['time', 'title', 'column'] as $type) {
      foreach (['Month', 'Week', 'Day'] as $range) {
        $key = $type . 'format' . $range;

        $form[$key] = [
          '#type'          => 'textfield',
          '#title'         => $this->t($range),
          '#default_value' => $style_plugin->options[$key],
          '#size'          => '30',
          '#fieldset'      => $type,
        ];

        if ($range != 'Day') {
          $form[$key]['#prefix'] = '<div class="views-left-30">';
          $form[$key]['#suffix'] = '</div>';
        }
      }
    }

    $form['time'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Time format'),
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/text/timeFormat', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#collapsible' => TRUE,
      '#open'        => TRUE,
      '#fieldset'    => 'times',
      '#prefix'      => '<div class="clearfix">',
      '#suffix'      => '</div>',
      '#states'      => [
        'visible' => [
          ':input[name="style_options[advanced]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];

    $form['title'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Title format'),
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/text/titleFormat', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#collapsible' => TRUE,
      '#open'        => TRUE,
      '#fieldset'    => 'times',
      '#prefix'      => '<div class="clearfix">',
      '#suffix'      => '</div>',
      '#states'      => [
        'visible' => [
          ':input[name="style_options[advanced]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];

    $form['column'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Column format'),
      '#description' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('http://arshaw.com/fullcalendar/docs/text/columnFormat', [
        'attributes' => [
          'target' => '_blank',
        ],
      ])),
      '#collapsible' => TRUE,
      '#open'        => TRUE,
      '#fieldset'    => 'times',
      '#prefix'      => '<div class="clearfix">',
      '#suffix'      => '</div>',
      '#states'      => [
        'visible' => [
          ':input[name="style_options[advanced]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
    ];

    $form['theme'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Use jQuery UI Theme'),
      '#default_value' => $style_plugin->options['theme'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'style',
    ];

    $form['sameWindow'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Open events in same window'),
      '#default_value' => $style_plugin->options['sameWindow'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'style',
      '#states'        => [
        'visible' => [
          ':input[name="style_options[modalWindow]"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];

    $form['modalWindow'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Open events in modal'),
      '#default_value' => $style_plugin->options['modalWindow'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'style',
      '#states'        => [
        'visible' => [
          ':input[name="style_options[sameWindow]"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];

    $form['contentHeight'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Calendar height'),
      '#size'          => 4,
      '#default_value' => $style_plugin->options['contentHeight'],
      '#field_suffix'  => 'px',
      '#data_type'     => 'int',
      '#fieldset'      => 'style',
    ];

    if ($this->moduleHandler->getImplementations('fullcalendar_droppable')) {
      $form['droppable'] = [
        '#type'          => 'checkbox',
        '#title'         => $this->t('Allow external events to be added via drag and drop'),
        '#default_value' => $style_plugin->options['droppable'],
        '#data_type'     => 'bool',
        '#fieldset'      => 'style',
      ];
    }

    $form['editable'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Prevent editing events via drag-and-drop'),
      '#default_value' => $style_plugin->options['editable'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'style',
      '#description'   => $this->t('Modules can set custom access rules, but this will override those.'),
    ];

    // Get the regular fields.
    $field_options = $style_plugin->displayHandler->getFieldLabels();
    // Get the date fields.
    $date_fields = $style_plugin->parseFields();

    $form['fields'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Customize fields'),
      '#description' => $this->t('Add fields to the view in order to customize fields below.'),
      '#collapsible' => TRUE,
      '#open'        => FALSE,
    ];

    $form['fields']['title'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Use a custom title'),
      '#default_value' => $style_plugin->options['fields']['title'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'fields',
    ];

    $form['fields']['title_field'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Title field'),
      '#options'       => $field_options,
      '#default_value' => $style_plugin->options['fields']['title_field'],
      '#description'   => $this->t('Choose the field with the custom title.'),
      '#process'       => ['\Drupal\Core\Render\Element\Select::processSelect'],
      '#states'        => [
        'visible' => [
          ':input[name="style_options[fields][title]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
      '#fieldset'      => 'fields',
    ];

    $form['fields']['url'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Use a custom redirect URL'),
      '#default_value' => $style_plugin->options['fields']['url'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'fields',
    ];

    $form['fields']['url_field'] = [
      '#type'          => 'select',
      '#title'         => $this->t('URL field'),
      '#options'       => $field_options,
      '#default_value' => $style_plugin->options['fields']['url_field'],
      '#description'   => $this->t('Choose the field with the custom link.'),
      '#process'       => ['\Drupal\Core\Render\Element\Select::processSelect'],
      '#states'        => [
        'visible' => [
          ':input[name="style_options[fields][url]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
      '#fieldset'      => 'fields',
    ];

    $form['fields']['date'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Use a custom date field'),
      '#default_value' => $style_plugin->options['fields']['date'],
      '#data_type'     => 'bool',
      '#fieldset'      => 'fields',
    ];

    $form['fields']['date_field'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Date fields'),
      '#options'       => $date_fields,
      '#default_value' => $style_plugin->options['fields']['date_field'],
      '#description'   => $this->t('Select one or more date fields.'),
      '#multiple'      => TRUE,
      '#size'          => count($date_fields),
      '#process'       => ['\Drupal\Core\Render\Element\Select::processSelect'],
      '#states'        => [
        'visible' => [
          ':input[name="style_options[fields][date]"]' => [
            'checked' => TRUE,
          ],
        ],
      ],
      '#fieldset'      => 'fields',
    ];

    // Disable form elements when not needed.
    if (empty($field_options)) {
      $form['fields']['#description'] = $this->t('All the options are hidden, you need to add fields first.');
      $form['fields']['title']['#type'] = 'hidden';
      $form['fields']['url']['#type'] = 'hidden';
      $form['fields']['date']['#type'] = 'hidden';
      $form['fields']['title_field']['#disabled'] = TRUE;
      $form['fields']['url_field']['#disabled'] = TRUE;
      $form['fields']['date_field']['#disabled'] = TRUE;
    }
    elseif (empty($date_fields)) {
      $form['fields']['date']['#type'] = 'hidden';
      $form['fields']['date_field']['#disabled'] = TRUE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state, &$options = []) {
    $options = $form_state->getValue('style_options');

    // These field options have empty defaults, make sure they stay that way.
    foreach (['title', 'url', 'date'] as $field) {
      if (empty($options['fields'][$field]) && isset($options['fields'][$field . '_field'])) {
        unset($options['fields'][$field . '_field']);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preView(&$settings) {
    if (empty($settings['editable'])) {
      $this->style->view->fullcalendar_disallow_editable = TRUE;
    }

    // TODO provide the ability to alter texts.
    $options = [
      'buttonText' => [
        'day'        => $this->t('Day'),
        'week'       => $this->t('Week'),
        'month'      => $this->t('Month'),
        'today'      => $this->t('Today'),
        'listDay'    => $this->t('List (day)'),
        'listWeek'   => $this->t('List (week)'),
        'listMonth'  => $this->t('List (month)'),
        'listYear'   => $this->t('List (year)'),
      ],
      'allDayText'      => $this->t('All day'),
      'monthNames'      => array_values(DateHelper::monthNames(TRUE)),
      'monthNamesShort' => array_values(DateHelper::monthNamesAbbr(TRUE)),
      'dayNames'        => DateHelper::weekDays(TRUE),
      'dayNamesShort'   => DateHelper::weekDaysAbbr(TRUE),
      'isRTL'           => $this->languageManager->getCurrentLanguage()
          ->getDirection() == 'rtl',
    ];

    $advanced = !empty($settings['advanced']);

    foreach ($settings as $key => $value) {
      if (is_array($value)) {
        continue;
      }
      elseif (in_array($key, ['left', 'center', 'right'])) {
        $options['header'][$key] = $value;
      }
      elseif (in_array($key, [
        'timeformatMonth',
        'timeformatWeek',
        'timeformatDay'
      ])) {
        if ($advanced) {
          // @see https://fullcalendar.io/docs/views/View-Specific-Options/
          $options['views'][strtolower(substr($key, 10))]['timeFormat'] = $value;
        }
      }
      elseif (in_array($key, [
        'columnformatMonth',
        'columnformatWeek',
        'columnformatDay'
      ])) {
        if ($advanced) {
          // @see https://fullcalendar.io/docs/views/View-Specific-Options/
          $options['views'][strtolower(substr($key, 12))]['columnFormat'] = $value;
        }
      }
      elseif (in_array($key, [
        'titleformatMonth',
        'titleformatWeek',
        'titleformatDay'
      ])) {
        if ($advanced) {
          // @see https://fullcalendar.io/docs/views/View-Specific-Options/
          $options['views'][strtolower(substr($key, 11))]['titleFormat'] = $value;
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
      elseif ($key == 'sameWindow' || $key == 'modalWindow') {
        // Keep this at the top level.
        continue;
      }
      else {
        $options[$key] = $value;
      }

      // Unset all values that have been migrated.
      unset($settings[$key]);
    }

    // Add display values.
    if (!empty($settings['display'])) {
      foreach ($settings['display'] as $key => $value) {
        $options[$key] = $value;
      }

      unset($settings['display']);
    }

    $settings['fullcalendar'] = $options;

    // First, use the default date if set.
    if (!empty($settings['times']['default_date'])) {
      list($date['year'], $date['month'], $date['date']) = explode('-', $settings['times']['date']);
      $settings['date'] = $date;
    }

    // Unset times settings.
    unset($settings['times']);

    // Get the values from the URL.
    extract($this->style->view->getExposedInput(), EXTR_SKIP);

    if (isset($year) && is_numeric($year)) {
      $settings['date']['year'] = $year;
    }

    if (isset($month) && is_numeric($month) && $month > 0 && $month <= 12) {
      $settings['date']['month'] = $month;
    }

    if (isset($day) && is_numeric($day) && $day > 0 && $day <= 31) {
      $settings['date']['date'] = $day;
    }

    if (isset($mode) && in_array($mode, [
        'month',
        'basicWeek',
        'basicDay',
        'agendaWeek',
        'agendaDay',
      ])
    ) {
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

}
