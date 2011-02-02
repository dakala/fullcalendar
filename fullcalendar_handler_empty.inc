<?php
// $Id$

class fullcalendar_handler_empty extends views_handler {
  function render() {
    fullcalendar_get_settings($this->view->style_options);
    return '<div id="fullcalendar"></div>';
  }
}
