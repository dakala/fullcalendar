<?php

namespace Drupal\fullcalendar\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * TODO
 *
 * @PluginID("fullcalendar_gcal")
 */
class GoogleCalendar extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function allow_advanced_render() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->query->add_field($this->view->storage->get('base_table'), $this->view->storage->get('base_field'));
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['label'] = [
      'default'      => $this->definition['title'],
      'translatable' => TRUE,
    ];

    $options['gcal'] = [
      'default' => '',
    ];

    $options['class'] = [
      'default' => 'fc-event-default fc-event-gcal',
    ];

    $options['timezone'] = [
      'default' => date_default_timezone_get(),
    ];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['label'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Label'),
      '#default_value' => isset($this->options['label']) ? $this->options['label'] : '',
      '#description'   => $this->t('The label for this field that will be displayed to end users if the style requires it.'),
    ];

    $form['gcal'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Feed URL'),
      '#maxlength'     => 1024,
      '#default_value' => $this->options['gcal'],
    ];

    $form['class'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('CSS class'),
      '#default_value' => $this->options['class'],
    ];

    $form['timezone'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Time zone'),
      '#default_value' => $this->options['timezone'],
      '#options'       => system_time_zones(),
      '#attributes'    => [
        'class' => [
          'timezone-detect',
        ],
      ],
    ];
  }

  public function getSettings() {
    return [
      $this->options['gcal'],
      [
        'editable'        => FALSE,
        'className'       => $this->options['class'],
        'currentTimezone' => $this->options['timezone'],
      ],
    ];
  }

}
