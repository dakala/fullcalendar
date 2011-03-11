<?php

/**
 * @file
 * Provide an empty region to display a blank FullCalendar.
 */

class fullcalendar_handler_empty extends views_handler_area {
  function render() {
    fullcalendar_get_settings($this->view->style_options);
    return '<div class="fullcalendar"></div>';
  }
}
