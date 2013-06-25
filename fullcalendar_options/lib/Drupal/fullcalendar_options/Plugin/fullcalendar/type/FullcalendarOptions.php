<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_options\Plugin\fullcalendar\type\Fullcalendar.
 */

namespace Drupal\fullcalendar_options\Plugin\fullcalendar\type;

use Drupal\fullcalendar\Annotation\FullcalendarOption;
use Drupal\fullcalendar\Plugin\FullcalendarBase;

/**
 * @todo.
 *
 * @FullcalendarOption(
 *   id = "fullcalendar_options",
 *   module = "fullcalendar_options",
 *   js = TRUE
 * )
 */
class FullcalendarOptions extends FullcalendarBase {

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::defineOptions().
   */
  public function defineOptions() {
    $options = array();
    foreach ($this->optionsListParsed() as $key => $info) {
      $options[$key]['default'] = $info['#default_value'];
      // If this is a Boolean value, set the 'bool' flag for export.
      if (isset($info['#data_type']) && $info['#data_type'] == 'bool') {
        $options[$key]['bool'] = TRUE;
      }
    }

    return array(
      'fullcalendar_options' => array(
        'contains' => $options,
      ),
    );
  }

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::buildOptionsForm().
   */
  public function buildOptionsForm(&$form, &$form_state) {
    $options = $this->optionsListParsed();
    // There were no options added, remove the parent fieldset.
    if (!empty($options)) {
      $form['fullcalendar_options'] = array(
        '#type' => 'details',
        '#title' => t('Extra options'),
        '#collapsed' => TRUE,
      );
      // Add the default value to each option.
      foreach ($options as $key => $info) {
        $form['fullcalendar_options'][$key] = $info;
        if (isset($this->style->options['fullcalendar_options'][$key])) {
          $form['fullcalendar_options'][$key]['#default_value'] = $this->style->options['fullcalendar_options'][$key];
        }
      }
    }
  }

  /**
   * @todo.
   */
  public static function optionsList() {
    $form = array();

    $form['firstHour'] = array(
      '#type' => 'textfield',
      '#title' => t('First hour'),
      '#description' => t('Determines the first hour that will be visible in the scroll pane.'),
      '#size' => 2,
      '#maxlength' => 2,
      '#default_value' => 6,
      '#data_type' => 'int',
    );
    $form['minTime'] = array(
      '#type' => 'textfield',
      '#title' => t('Minimum time'),
      '#description' => t('Determines the first hour/time that will be displayed, even when the scrollbars have been scrolled all the way up.'),
      '#size' => 2,
      '#maxlength' => 2,
      '#default_value' => 0,
      '#data_type' => 'int',
    );
    $form['maxTime'] = array(
      '#type' => 'textfield',
      '#title' => t('Maximum time'),
      '#description' => t('Determines the last hour/time (exclusively) that will be displayed, even when the scrollbars have been scrolled all the way down.'),
      '#size' => 2,
      '#maxlength' => 2,
      '#default_value' => 24,
      '#data_type' => 'int',
    );
    $form['slotMinutes'] = array(
      '#type' => 'textfield',
      '#title' => t('Slot minutes'),
      '#description' => t('The frequency for displaying time slots, in minutes.'),
      '#size' => 2,
      '#maxlength' => 2,
      '#default_value' => 30,
      '#data_type' => 'int',
    );
    $form['defaultEventMinutes'] = array(
      '#type' => 'textfield',
      '#title' => t('Default event minutes'),
      '#description' => t('Determines the length (in minutes) an event appears to be when it has an unspecified end date.'),
      '#size' => 4,
      '#maxlength' => 4,
      '#default_value' => 120,
      '#data_type' => 'int',
    );
    $form['allDaySlot'] = array(
      '#type' => 'checkbox',
      '#title' => t('All day slot'),
      '#description' => t('Determines if the "all-day" slot is displayed at the top of the calendar.'),
      '#default_value' => TRUE,
      '#data_type' => 'bool',
    );
    $form['weekends'] = array(
      '#type' => 'checkbox',
      '#title' => t('Weekends'),
      '#description' => t('Whether to include Saturday/Sunday columns in any of the calendar views.'),
      '#default_value' => TRUE,
      '#data_type' => 'bool',
    );
    $form['lazyFetching'] = array(
      '#type' => 'checkbox',
      '#title' => t('Lazy fetching'),
      '#description' => t('Determines when event fetching should occur.'),
      '#default_value' => TRUE,
      '#data_type' => 'bool',
    );
    $form['disableDragging'] = array(
      '#type' => 'checkbox',
      '#title' => t('Disable dragging'),
      '#description' => t('Disables all event dragging, even when events are editable.'),
      '#default_value' => FALSE,
      '#data_type' => 'bool',
    );
    $form['disableResizing'] = array(
      '#type' => 'checkbox',
      '#title' => t('Disable resizing'),
      '#description' => t('Disables all event resizing, even when events are editable.'),
      '#default_value' => FALSE,
      '#data_type' => 'bool',
    );
    $form['dragRevertDuration'] = array(
      '#type' => 'textfield',
      '#title' => t('Drag revert duration'),
      '#description' => t('Time (in ms) it takes for an event to revert to its original position after an unsuccessful drag.'),
      '#size' => 6,
      '#maxlength' => 6,
      '#default_value' => 500,
      '#data_type' => 'int',
    );
    $form['dayClick'] = array(
      '#type' => 'checkbox',
      '#title' => t('Day click'),
      '#description' => t('Switch the display when a day is clicked'),
      '#default_value' => FALSE,
      '#data_type' => 'bool',
    );
    return $form;
  }

  /**
   * @todo.
   */
  protected function optionsListParsed() {
    $form = static::optionsList();
    // By default, restrict the form to options allowed by the admin settings.
    $form = array_intersect_key($form, array_filter(config('fullcalendar_options.settings')->get()));

    if (isset($form['dayClick'])) {
      // Add in dependency form elements.
      $form['dayClickView'] = array(
        '#type' => 'select',
        '#title' => t('Display'),
        '#description' => t('The display to switch to when a day is clicked.'),
        '#default_value' => 'agendaWeek',
        '#options' => array(
          'month' => t('Month'),
          'agendaWeek' => t('Week (Agenda)'),
          'basicWeek' => t('Week (Basic)'),
          'agendaDay' => t('Day (Agenda)'),
          'basicDay' => t('Day (Basic)'),
        ),
        '#states' => array(
          'visible' => array(
            ':input[name="style_options[fullcalendar_options][dayClick]"]' => array('checked' => TRUE),
          ),
        ),
      );
    }

    return $form;
  }

}
