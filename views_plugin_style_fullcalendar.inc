<?php
// $Id$

/**
 * @file
 * Contains the fullcalendar style plugin.
 */

class views_plugin_style_fullcalendar extends views_plugin_style {
  function option_definition() {
    $options = parent::option_definition();
    $options['display'] = array(
      'contains' => array(
        'fc_view' => array('default' => 'month'),
        'fc_firstday' => array('default' => '0'),
        'fc_weekmode' => array('default' => 'fixed'),
      ),
    );
    $options['modules'] = array(
      'contains' => array(
        'fc_theme' => array('default' => FALSE),
        'fc_url_colorbox' => array('default' => FALSE),
      ),
    );
    $options['header'] = array(
      'contains' => array(
        'fc_left' => array('default' => 'today prev,next'),
        'fc_center' => array('default' => 'title'),
        'fc_right' => array('default' => 'month agendaWeek agendaDay'),
      ),
    );
    $options['defaults'] = array(
      'contains' => array(
        'fc_year' => array('default' => ''),
        'fc_month' => array('default' => ''),
        'fc_day' => array('default' => ''),
      ),
    );
    $options['times'] = array(
      'contains' => array(
        'fc_timeformat' => array('default' => 'h:mm{ - h:mm}'),
        'fc_clock' => array('default' => FALSE),
      ),
    );
    return $options;
  }

  function options_form(&$form, &$form_state) {
    $form['display'] = array(
      '#type' => 'fieldset',
      '#title' => t('Display settings'),
      '#description' => 'Blank values will use the current day, month, or year.',
      '#attributes' => array(
        'class' => 'clear-block',
      ),
    );
    $form['display']['fc_view'] = array(
      '#type' => 'select',
      '#title' => t('Initial display'),
      '#options' => array(
        'month' => 'Month',
        'agendaWeek' => 'Week (Agenda)',
        'basicWeek' => 'Week (Basic)',
        'agendaDay' => 'Day (Agenda)',
        'basicDay' => 'Day (Basic)',
      ),
      '#default_value' => $this->options['display']['fc_view'],
      '#description' => l(t('Default timespan displayed.'), 'http://arshaw.com/fullcalendar/docs/views/Available_Views', array('attributes' => array('target' => '_blank'))),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
    );
    $form['display']['fc_firstday'] = array(
      '#type' => 'select',
      '#title' => t('First day'),
      '#options' => array(
        '0' => 'Sunday',
        '1' => 'Monday',
      ),
      '#default_value' => $this->options['display']['fc_firstday'],
      '#description' => l(t('The day each week begins.'), 'http://arshaw.com/fullcalendar/docs/display/firstDay', array('attributes' => array('target' => '_blank'))),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
    );
    $form['display']['fc_weekmode'] = array(
      '#type' => 'select',
      '#title' => t('Week mode'),
      '#options' => array(
        'fixed' => 'Fixed',
        'liquid' => 'Liquid',
        'variable' => 'Variable',
      ),
      '#default_value' => $this->options['display']['fc_weekmode'],
      '#description' => l(t('Number of weeks displayed.'), 'http://arshaw.com/fullcalendar/docs/display/weekMode', array('attributes' => array('target' => '_blank'))),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
    );
    $form['header'] = array(
      '#type' => 'fieldset',
      '#title' => t('Header elements'),
      '#description' => l(t('Buttons and title to be shown in header.'), 'http://arshaw.com/fullcalendar/docs/display/header', array('attributes' => array('target' => '_blank'))),
      '#attributes' => array(
        'class' => 'clear-block',
      ),
    );
    $form['header']['fc_left'] = array(
      '#type' => 'textfield',
      '#title' => t('Left header'),
      '#default_value' => $this->options['header']['fc_left'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '20',
    );
    $form['header']['fc_center'] = array(
      '#type' => 'textfield',
      '#title' => t('Center header'),
      '#default_value' => $this->options['header']['fc_center'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '20',
    );
    $form['header']['fc_right'] = array(
      '#type' => 'textfield',
      '#title' => t('Right header'),
      '#default_value' => $this->options['header']['fc_right'],
      '#prefix' => '<div class="views-left-40">',
      '#suffix' => '</div>',
      '#size' => '30',
    );
    $form['defaults'] = array(
      '#type' => 'fieldset',
      '#title' => t('Default values'),
      '#description' => 'Blank values will use the current day, month, or year.',
      '#attributes' => array(
        'class' => 'clear-block',
      ),
    );
    $form['defaults']['fc_year'] = array(
      '#type' => 'textfield',
      '#title' => t('Year'),
      '#default_value' => $this->options['defaults']['fc_year'],
      '#description' => t('Full 4 digits.'),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '20',
    );
    $form['defaults']['fc_month'] = array(
      '#type' => 'textfield',
      '#title' => t('Month'),
      '#default_value' => $this->options['defaults']['fc_month'],
      '#description' => t('No leading zeros.'),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '20',
    );
    $form['defaults']['fc_day'] = array(
      '#type' => 'textfield',
      '#title' => t('Day'),
      '#default_value' => $this->options['defaults']['fc_day'],
      '#description' => t('No leading zeros.'),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '20',
    );
    $form['times'] = array(
      '#type' => 'fieldset',
      '#title' => t('Time display'),
      '#description' => l(t('Formatting options for time.'), 'http://arshaw.com/fullcalendar/docs/utilities/formatDate', array('attributes' => array('target' => '_blank'))),
      '#attributes' => array(
        'class' => 'clear-block',
      ),
    );
    $form['times']['fc_timeformat'] = array(
      '#type' => 'textfield',
      '#title' => t('Time format'),
      '#default_value' => $this->options['times']['fc_timeformat'],
      '#description' => 'Format of time display on each event.',
    );
    $form['times']['fc_clock'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use 24 hour display'),
      '#default_value' => $this->options['times']['fc_clock'],
      '#description' => 'Use 24 display (ignores custom time format).',
    );
    $form['modules'] = array(
      '#type' => 'fieldset',
      '#title' => t('Module integration'),
      '#description' => 'Settings for integration with other modules.',
      '#attributes' => array(
        'class' => 'clear-block',
      ),
    );
    $form['modules']['fc_theme'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use jQuery UI Theme'),
      '#default_value' => $this->options['modules']['fc_theme'],
      '#description' => t('If checked, the calendar will use any loaded jQuery UI themes.'),
    );
    $form['modules']['fc_url_colorbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open events in colorbox'),
      '#default_value' => $this->options['modules']['fc_url_colorbox'],
      '#description' => t('If checked, each event will open in colorbox (if it is installed).'),
    );
  }
}
