<?php

/**
 * @file
 * Provides extra FullCalendar configuration options for Colorbox.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_fullcalendar_options_info().
 */
function colorbox_fullcalendar_options_info() {
  return [
    'colorbox' => [
      'name'   => t('Colorbox'),
      'js'     => TRUE,
      'parent' => 'fullcalendar_options',
    ],
  ];
}

/**
 * Implements hook_fullcalendar_options_definition().
 */
function colorbox_fullcalendar_options_definition() {
  $options['colorbox']['contains'] = [
    'colorbox'       => [
      'default' => FALSE,
      'bool'    => TRUE,
    ],
    'colorboxIFrame' => [
      'default' => FALSE,
      'bool'    => TRUE,
    ],
    'colorboxClass'  => [
      'default' => '#content',
    ],
    'colorboxWidth'  => [
      'default' => '80%',
    ],
    'colorboxHeight' => [
      'default' => '80%',
    ],
  ];

  return $options;
}

/**
 * Implements hook_fullcalendar_options_form().
 */
function colorbox_fullcalendar_options_form(&$form, FormStateInterface $form_state, &$view) {
  $form['colorbox']['colorbox'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Open events with Colorbox'),
    '#default_value' => $view->options['colorbox']['colorbox'],
    '#data_type'     => 'bool',
  ];

  $form['colorbox']['colorboxIFrame'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Open events in iFrame'),
    '#default_value' => $view->options['colorbox']['colorboxIFrame'],
    '#dependency'    => [
      'edit-style-options-colorbox-colorbox' => [
        1,
      ],
    ],
    '#data_type'     => 'bool',
  ];

  $form['colorbox']['colorboxClass'] = [
    '#type'          => 'textfield',
    '#title'         => t('Classname or ID selector'),
    '#default_value' => $view->options['colorbox']['colorboxClass'],
    '#dependency'    => [
      'edit-style-options-colorbox-colorbox' => [
        1,
      ],
    ],
  ];

  $form['colorbox']['colorboxWidth'] = [
    '#type'          => 'textfield',
    '#title'         => t('Width'),
    '#default_value' => $view->options['colorbox']['colorboxWidth'],
    '#dependency'    => [
      'edit-style-options-colorbox-colorbox' => [
        1,
      ],
    ],
  ];

  $form['colorbox']['colorboxHeight'] = [
    '#type'          => 'textfield',
    '#title'         => t('Height'),
    '#default_value' => $view->options['colorbox']['colorboxHeight'],
    '#dependency'    => [
      'edit-style-options-colorbox-colorbox' => [
        1,
      ],
    ],
  ];

  $form['sameWindow']['#dependency'] = [
    'edit-style-options-colorbox-colorbox' => [
      0,
    ],
  ];
}

/**
 * Implements hook_fullcalendar_options_process().
 */
function colorbox_fullcalendar_options_process(&$variables, &$settings) {
  if (empty($settings['colorbox']['colorbox'])) {
    unset($settings['colorbox']);
  }
}
