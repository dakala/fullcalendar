<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_options\Plugin\fullcalendar\type\Fullcalendar.
 */

namespace Drupal\fullcalendar_options\Plugin\fullcalendar\type;

use Drupal\Core\Annotation\Plugin;
use Drupal\fullcalendar\Plugin\FullcalendarBase;

/**
 * @todo.
 *
 * @Plugin(
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
    foreach (_fullcalendar_options_list() as $key => $info) {
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
    $options = _fullcalendar_options_list();
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

}
