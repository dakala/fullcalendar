<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\fullcalendar\type\Fullcalendar.
 */

namespace Drupal\fullcalendar\Plugin\fullcalendar\type;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Language\Language;
use Drupal\fullcalendar\Annotation\FullcalendarOption;
use Drupal\datetime\DateHelper;
use Drupal\fullcalendar\Plugin\FullcalendarBase;

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
class Fullcalendar extends FullcalendarBase {

  /**
   * @todo.
   *
   * @var array
   */
  protected static $formats = array(
    '12' => array(
      'time' => 'h:mm{ - h:mm}',
      'axis' => 'h(:mm)t',
    ),
    '24' => array(
      'time' => 'HH:mm{ - HH:mm}',
      'axis' => 'HH(:mm)',
    ),
    'mdy' => array(
      'title' => array(
        'month' => 'MMMM yyyy',
        'week' => 'MMM d[ yyyy]{ - [ MMM] d yyyy}',
        'day' => 'dddd, MMM d, yyyy',
      ),
      'column' => array(
        'month' => 'ddd',
        'week' => 'ddd M/d',
        'day' => 'dddd M/d',
      ),
    ),
    'dmy' => array(
      'title' => array(
        'month' => 'MMMM yyyy',
        'week' => 'd [ MMM][ yyyy]{ - d MMM yyyy}',
        'day' => 'dddd, d MMM, yyyy',
      ),
      'column' => array(
        'month' => 'ddd',
        'week' => 'ddd d/M',
        'day' => 'dddd d/M',
      ),
    ),
    'ymd' => array(
      'title' => array(
        'month' => 'yyyy MMMM',
        'week' => 'yyyy MMM d{ - [ yyyy][ MMM] d}',
        'day' => 'yyyy MMM d, dddd ',
      ),
      'column' => array(
        'month' => 'ddd',
        'week' => 'ddd M/d',
        'day' => 'dddd M/d',
      ),
    ),
  );

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::defineOptions().
   */
  public function defineOptions() {
    $time = '12';
    $date = 'mdy';
    $time_format = static::$formats[$time];
    $date_format = static::$formats[$date];
    $options = array(
      'defaultView' => array('default' => 'month'),
      'firstDay' => array('default' => '0'),
      'weekMode' => array('default' => 'fixed'),
      'left' => array('default' => 'today prev,next'),
      'center' => array('default' => 'title'),
      'right' => array('default' => 'month agendaWeek agendaDay'),
      'timeformat' => array('default' => $time_format['time']),
      'advanced' => array(
        'default' => FALSE,
      ),
      'axisFormat' => array('default' => $time_format['axis']),
      'timeformatMonth' => array('default' => $time_format['time']),
      'titleformatMonth' => array('default' => $date_format['title']['month']),
      'columnformatMonth' => array('default' => $date_format['column']['month']),
      'timeformatWeek' => array('default' => $time_format['time']),
      'titleformatWeek' => array('default' => $date_format['title']['week']),
      'columnformatWeek' => array('default' => $date_format['column']['week']),
      'timeformatDay' => array('default' => $time_format['time']),
      'titleformatDay' => array('default' => $date_format['title']['day']),
      'columnformatDay' => array('default' => $date_format['column']['day']),
      'theme' => array(
        'default' => TRUE,
      ),
      'sameWindow' => array(
        'default' => FALSE,
      ),
      'contentHeight' => array('default' => 0),
      'droppable' => array(
        'default' => FALSE,
      ),
      'editable' => array(
        'default' => FALSE,
      ),
    );

    // Nest these explicitly so that they can be more easily found later.
    $options['times'] = array(
      'contains' => array(
        'default_date' => array(
          'default' => FALSE,
        ),
        'date' => array(
          'default' => array(
            'year' => '1900',
            'month' => '1',
            'day' => '1',
          ),
        ),
      ),
    );
    $options['fields'] = array(
      'contains' => array(
        'title_field' => array('default' => 'title'),
        'url_field' => array('default' => 'title'),
        'date_field' => array('default' => array()),
        'title' => array(
          'default' => FALSE,
        ),
        'url' => array(
          'default' => FALSE,
        ),
        'date' => array(
          'default' => FALSE,
        ),
      ),
    );
    return $options;
  }

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::process().
   */
  public function process(&$settings) {
    static $fc_dom_id = 1;
    if (empty($this->style->view->dom_id)) {
      $this->style->view->dom_id = 'fc-' . $fc_dom_id++;
    }

    $options = $this->style->options;

    $options['gcal'] = array();
    foreach ($this->style->view->field as $field) {
      if ($field->field == 'gcal') {
        $options['gcal'][] = $field->getSettings();
      }
    }

    unset($options['fields']);

    $settings += $options + array(
      'view_name' => $this->style->view->storage->id(),
      'view_display' => $this->style->view->current_display,
    );
  }

  public function buildOptionsForm(&$form, &$form_state) {
    $form['#pre_render'][] = 'views_ui_pre_render_add_fieldset_markup';

    $form['display'] = array(
      '#type' => 'details',
      '#title' => t('Display settings'),
      '#collapsible' => TRUE,
    );
    $form['header'] = array(
      '#type' => 'details',
      '#title' => t('Header settings'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/display/header', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['times'] = array(
      '#type' => 'details',
      '#title' => t('Time/date settings'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/utilities/formatDate', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['style'] = array(
      '#type' => 'details',
      '#title' => t('Style settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );

    $form['defaultView'] = array(
      '#type' => 'select',
      '#title' => t('Initial display'),
      '#options' => array(
        'month' => 'Month',
        'agendaWeek' => 'Week (Agenda)',
        'basicWeek' => 'Week (Basic)',
        'agendaDay' => 'Day (Agenda)',
        'basicDay' => 'Day (Basic)',
      ),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/views/Available_Views', array('attributes' => array('target' => '_blank'))),
      '#default_value' => $this->style->options['defaultView'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#fieldset' => 'display',
    );
    $form['firstDay'] = array(
      '#type' => 'select',
      '#title' => t('Week starts on'),
      '#options' => DateHelper::weekDays(TRUE),
      '#default_value' => $this->style->options['firstDay'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#fieldset' => 'display',
    );
    $form['weekMode'] = array(
      '#type' => 'select',
      '#title' => t('Week mode'),
      '#options' => array(
        'fixed' => 'Fixed',
        'liquid' => 'Liquid',
        'variable' => 'Variable',
      ),
      '#default_value' => $this->style->options['weekMode'],
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/display/weekMode', array('attributes' => array('target' => '_blank'))),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#fieldset' => 'display',
    );
    $form['left'] = array(
      '#type' => 'textfield',
      '#title' => t('Left'),
      '#default_value' => $this->style->options['left'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'header',
    );
    $form['center'] = array(
      '#type' => 'textfield',
      '#title' => t('Center'),
      '#default_value' => $this->style->options['center'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'header',
    );
    $form['right'] = array(
      '#type' => 'textfield',
      '#title' => t('Right'),
      '#default_value' => $this->style->options['right'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'header',
    );
    $form['times']['default_date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom initial date'),
      '#description' => t('If unchecked, the calendar will load the current date.'),
      '#default_value' => $this->style->options['times']['default_date'],
      '#data_type' => 'bool',
      '#fieldset' => 'times',
    );
    $form['times']['date'] = array(
      '#type' => 'date',
      '#title' => t('Custom initial date'),
      '#title_display' => 'invisible',
      '#default_value' => $this->style->options['times']['date'],
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[times][default_date]"]' => array('checked' => TRUE),
        ),
      ),
      '#fieldset' => 'times',
    );
    $form['timeformat'] = array(
      '#type' => 'textfield',
      '#title' => t('Time format'),
      '#default_value' => $this->style->options['timeformat'],
      '#size' => '30',
      '#fieldset' => 'times',
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[advanced]"]' => array('checked' => FALSE),
        ),
      ),
    );
    $form['advanced'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable advanced time and date format settings'),
      '#default_value' => $this->style->options['advanced'],
      '#data_type' => 'bool',
      '#fieldset' => 'times',
    );
    $form['axisFormat'] = array(
      '#type' => 'textfield',
      '#title' => t('Axis format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/agenda/axisFormat', array('attributes' => array('target' => '_blank'))),
      '#default_value' => $this->style->options['axisFormat'],
      '#size' => '30',
      '#fieldset' => 'times',
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[advanced]"]' => array('checked' => TRUE),
        ),
      ),
    );

    // Add the nine time/date formats.
    foreach (array('time', 'title', 'column') as $type) {
      foreach (array('Month', 'Week', 'Day') as $range) {
        $key = $type . 'format' . $range;
        $form[$key] = array(
          '#type' => 'textfield',
          '#title' => t($range),
          '#default_value' => $this->style->options[$key],
          '#prefix' => '<div class="views-left-30">',
          '#suffix' => '</div>',
          '#size' => '30',
          '#fieldset' => $type,
        );
      }
    }

    $form['time'] = array(
      '#type' => 'details',
      '#title' => t('Time format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/text/timeFormat', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#fieldset' => 'times',
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[advanced]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['title'] = array(
      '#type' => 'details',
      '#title' => t('Title format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/text/titleFormat', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#fieldset' => 'times',
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[advanced]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['column'] = array(
      '#type' => 'details',
      '#title' => t('Column format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/text/columnFormat', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#fieldset' => 'times',
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[advanced]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['theme'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use jQuery UI Theme'),
      '#default_value' => $this->style->options['theme'],
      '#data_type' => 'bool',
      '#fieldset' => 'style',
    );
    $form['sameWindow'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open events in same window'),
      '#default_value' => $this->style->options['sameWindow'],
      '#data_type' => 'bool',
      '#fieldset' => 'style',
    );
    $form['contentHeight'] = array(
      '#type' => 'textfield',
      '#title' => t('Calendar height'),
      '#size' => 4,
      '#default_value' => $this->style->options['contentHeight'],
      '#field_suffix' => 'px',
      '#data_type' => 'int',
      '#fieldset' => 'style',
    );
    if (module_implements('fullcalendar_droppable')) {
      $form['droppable'] = array(
        '#type' => 'checkbox',
        '#title' => t('Allow external events to be added via drag and drop'),
        '#default_value' => $this->style->options['droppable'],
        '#data_type' => 'bool',
        '#fieldset' => 'style',
      );
    }
    $form['editable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Prevent editing events via drag-and-drop'),
      '#default_value' => $this->style->options['editable'],
      '#data_type' => 'bool',
      '#fieldset' => 'style',
      '#description' => t('Modules can set custom access rules, but this will override those.'),
    );

    // Get the regular fields.
    $field_options = $this->style->displayHandler->getFieldLabels();
    // Get the date fields.
    $date_fields = $this->style->parseFields();

    $form['fields'] = array(
      '#type' => 'details',
      '#title' => t('Customize fields'),
      '#description' => t('Add fields to the view in order to customize fields below.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['fields']['title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom title'),
      '#default_value' => $this->style->options['fields']['title'],
      '#data_type' => 'bool',
      '#fieldset' => 'fields',
    );
    $form['fields']['title_field'] = array(
      '#type' => 'select',
      '#title' => t('Title field'),
      '#options' => $field_options,
      '#default_value' => $this->style->options['fields']['title_field'],
      '#description' => t('Choose the field with the custom title.'),
      '#process' => array('form_process_select'),
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[fields][title]"]' => array('checked' => TRUE),
        ),
      ),
      '#fieldset' => 'fields',
    );
    $form['fields']['url'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom redirect URL'),
      '#default_value' => $this->style->options['fields']['url'],
      '#data_type' => 'bool',
      '#fieldset' => 'fields',
    );
    $form['fields']['url_field'] = array(
      '#type' => 'select',
      '#title' => t('URL field'),
      '#options' => $field_options,
      '#default_value' => $this->style->options['fields']['url_field'],
      '#description' => t('Choose the field with the custom link.'),
      '#process' => array('form_process_select'),
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[fields][url]"]' => array('checked' => TRUE),
        ),
      ),
      '#fieldset' => 'fields',
    );
    $form['fields']['date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom date field'),
      '#default_value' => $this->style->options['fields']['date'],
      '#data_type' => 'bool',
      '#fieldset' => 'fields',
    );
    $form['fields']['date_field'] = array(
      '#type' => 'select',
      '#title' => t('Date fields'),
      '#options' => $date_fields,
      '#default_value' => $this->style->options['fields']['date_field'],
      '#description' => t('Select one or more date fields.'),
      '#multiple' => TRUE,
      '#size' => count($date_fields),
      '#process' => array('form_process_select'),
      '#states' => array(
        'visible' => array(
          ':input[name="style_options[fields][date]"]' => array('checked' => TRUE),
        ),
      ),
      '#fieldset' => 'fields',
    );

    // Disable form elements when not needed.
    if (empty($field_options)) {
      $form['fields']['#description'] = t('All the options are hidden, you need to add fields first.');
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
   * @todo.
   */
  public function submitOptionsForm(&$form, &$form_state) {
    $options = &$form_state['values']['style_options'];

    // These field options have empty defaults, make sure they stay that way.
    foreach (array('title', 'url', 'date') as $field) {
      if (empty($options['fields'][$field])) {
        unset($options['fields'][$field . '_field']);
      }
    }
  }

  public function preView(&$settings) {
    global $language;

    if (!empty($settings['editable'])) {
      $this->style->view->fullcalendar_disallow_editable = TRUE;
    }

    $options = array(
      'buttonText' => array(
        'day' => t('Day'),
        'week' => t('Week'),
        'month' => t('Month'),
        'today' => t('Today'),
      ),
      'allDayText' => t('All day'),
      'monthNames' => array_values(DateHelper::monthNames(TRUE)),
      'monthNamesShort' => array_values(DateHelper::monthNamesAbbr(TRUE)),
      'dayNames' => DateHelper::weekDays(TRUE),
      'dayNamesShort' => DateHelper::weekDaysAbbr(TRUE),
      'isRTL' => (bool) \Drupal::service('language_manager')->getLanguage(Language::TYPE_INTERFACE)->direction,
    );
    $advanced = !empty($settings['advanced']);
    foreach ($settings as $key => $value) {
      if (is_array($value)) {
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

}
