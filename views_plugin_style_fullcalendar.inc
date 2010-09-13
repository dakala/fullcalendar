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
    $options['fullcalendar_url_colorbox'] = array('default' => FALSE);
    $options['fullcalendar_header_left'] = array('default' => 'today prev,next');
    $options['fullcalendar_header_center'] = array('default' => 'title');
    $options['fullcalendar_header_right'] = array('default' => 'month agendaWeek agendaDay');
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
    $form['fullcalendar_url_colorbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open events in colorbox'),
      '#default_value' => $this->options['fullcalendar_url_colorbox'],
      '#description' => t('If checked, each event\'s url will open in a colorbox (if it is installed).'),
    );
    $form['fullcalendar_header_left'] = array(
      '#type' => 'textfield',
      '#title' => t('FullCalendar header left'),
      '#default_value' => $this->options['fullcalendar_header_left'],
      '#description' => "<a href='http://arshaw.com/fullcalendar/docs/display/header/' target='_blank'>http://arshaw.com/fullcalendar/docs/display/header/</a>",
    );
    $form['fullcalendar_header_center'] = array(
      '#type' => 'textfield',
      '#title' => t('FullCalendar header center'),
      '#default_value' => $this->options['fullcalendar_header_center'],
      '#description' => "<a href='http://arshaw.com/fullcalendar/docs/display/header/' target='_blank'>http://arshaw.com/fullcalendar/docs/display/header/</a>",
    );
    $form['fullcalendar_header_right'] = array(
      '#type' => 'textfield',
      '#title' => t('FullCalendar header right'),
      '#default_value' => $this->options['fullcalendar_header_right'],
      '#description' => "<a href='http://arshaw.com/fullcalendar/docs/display/header/' target='_blank'>http://arshaw.com/fullcalendar/docs/display/header/</a>",
    );
  }
}