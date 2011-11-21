<?php

/**
 * @file
 * Provide an empty region to display a blank FullCalendar.
 */

class fullcalendar_handler_area_empty extends views_handler_area {
  function render($empty = FALSE) {
    $variables = array('view' => $this->view);
    fullcalendar_get_settings($variables);
    return '<div class="fullcalendar-status"></div><div class="fullcalendar"></div><div class="fullcalendar-content"></div>';
  }
}
