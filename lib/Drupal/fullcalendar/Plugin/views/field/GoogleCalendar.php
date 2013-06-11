<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\views\field\FullCalendar.
 */

namespace Drupal\fullcalendar\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\Component\Annotation\PluginID;

/**
 * @todo.
 *
 * @PluginID("fullcalendar_gcal")
 */
class GoogleCalendar extends FieldPluginBase {

  /**
   * Overrides \Drupal\views\Plugin\views\field\FieldPluginBase::allow_advanced_render().
   */
  public function allow_advanced_render() {
    return FALSE;
  }

  /**
   * Overrides \Drupal\views\Plugin\views\field\FieldPluginBase::query().
   */
  public function query() {
    $this->query->add_field($this->view->storage->get('base_table'), $this->view->storage->get('base_field'));
  }

  /**
   * Overrides \Drupal\views\Plugin\views\field\FieldPluginBase::defineOptions().
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['label'] = array('default' => $this->definition['title'], 'translatable' => TRUE);
    $options['gcal'] = array('default' => '');
    $options['class'] = array('default' => 'fc-event-default fc-event-gcal');
    $options['timezone'] = array('default' => date_default_timezone_get());
    return $options;
  }

  /**
   * Overrides \Drupal\views\Plugin\views\field\FieldPluginBase::buildOptionsForm().
   */
  public function buildOptionsForm(&$form, &$form_state) {
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => isset($this->options['label']) ? $this->options['label'] : '',
      '#description' => t('The label for this field that will be displayed to end users if the style requires it.'),
    );
    $form['gcal'] = array(
      '#type' => 'textfield',
      '#title' => t('Feed URL'),
      '#maxlength' => 1024,
      '#default_value' => $this->options['gcal'],
    );
    $form['class'] = array(
      '#type' => 'textfield',
      '#title' => t('CSS class'),
      '#default_value' => $this->options['class'],
    );
    $form['timezone'] = array(
      '#type' => 'select',
      '#title' => t('Time zone'),
      '#default_value' => $this->options['timezone'],
      '#options' => system_time_zones(),
      '#attributes' => array('class' => array('timezone-detect')),
    );
  }

  public function getSettings() {
    return array(
      $this->options['gcal'],
      array(
        'editable' => FALSE,
        'className' => $this->options['class'],
        'currentTimezone' => $this->options['timezone'],
      ),
    );
  }

}
