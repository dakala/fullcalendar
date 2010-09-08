<?php
// $Id$
/**
 * @file
 * Contains the fullcalendar style plugin.
 */

class views_plugin_style_fullcalendar extends views_plugin_style {
    
  function option_definition() {
    $options = parent::option_definition();
    $options['fullcalendar_view'] = array('default' => 'month');
    $options['fullcalendar_theme'] = array('default' => FALSE);
    $options['url_colorbox'] = array('default' => FALSE);
    return $options;
  }

  function options_form(&$form, &$form_state) {
    //parent::options_form($form, $form_state);
    $form['fullcalendar_view'] = array(
      '#type' => 'select',
      '#title' => t('Default Timespan'),
      '#options' => array("month" => "month", "basicWeek" => "basicWeek", "basicDay" => "basicDay", "agendaWeek" => "agendaWeek", "agendaDay" => "agendaDay"),
      '#default_value' => $this->options['fullcalendar_view'],
      '#description' => t('Default timespan presented to user.'),
    );
    $form['fullcalendar_theme'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use JQuery UI Theme'),
      '#default_value' => $this->options['fullcalendar_theme'],
      '#description' => t('If checked, the calendar will use any loaded jquery ui themes.'),
    );
    $form['url_colorbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open Event in colorbox'),
      '#default_value' => $this->options['url_colorbox'],
      '#description' => t('If checked, each event\'s url will open in a colorbox (if it is installed).'),
    );
  }
}