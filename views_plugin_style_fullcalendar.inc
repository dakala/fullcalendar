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
    $options['fullcalendar_weekmode'] = array('default' => 'fixed');
    $options['fullcalendar_defaultyear'] = array('default' => '');
    $options['fullcalendar_defaultmonth'] = array('default' => '');
    $options['fullcalendar_defaultday'] = array('default' => '');
    $options['fullcalendar_timeformat'] = array('default' => 'h:mm{ - h:mm}');
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
    $form['fullcalendar_weekmode'] = array(
      '#type' => 'select',
      '#title' => t('weekMode'),
      '#options' => array("fixed" => "fixed", "liquid" => "liquid", "variable" => "variable"),
      '#default_value' => $this->options['fullcalendar_weekmode'],
      '#description' => "<a href='http://arshaw.com/fullcalendar/docs/display/weekMode/' target='_blank'>http://arshaw.com/fullcalendar/docs/display/weekMode/</a>",
    );
    $form['fullcalendar_defaultyear'] = array(
      '#type' => 'textfield',
      '#title' => t('Default year'),
      '#default_value' => $this->options['fullcalendar_defaultyear'],
      '#description' => t('A full numeric representation of a year, 4 digits. Leave blank for current year.'),
    );
    $form['fullcalendar_defaultmonth'] = array(
      '#type' => 'textfield',
      '#title' => t('Default month'),
      '#default_value' => $this->options['fullcalendar_defaultmonth'],
      '#description' => t('Numeric representation of a month, without leading zeros. Leave blank for current month.'),
    );
    $form['fullcalendar_defaultday'] = array(
      '#type' => 'textfield',
      '#title' => t('Default day'),
      '#default_value' => $this->options['fullcalendar_defaultday'],
      '#description' => t('Day of the month without leading zeros. Leave blank for current day.'),
    );
    $form['fullcalendar_timeformat'] = array(
      '#type' => 'textfield',
      '#title' => t('Time format'),
      '#default_value' => $this->options['fullcalendar_timeformat'],
      '#description' => "<a href='http://arshaw.com/fullcalendar/docs/text/timeFormat/' target='_blank'>http://arshaw.com/fullcalendar/docs/text/timeFormat/</a>",
    );
  }
}
