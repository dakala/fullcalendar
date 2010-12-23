<?php
// $Id$

/**
 * @file
 * Contains the view display plugin.
 */

/**
 * Forces the calendar to display a fake node if none are returned.
 */
class fullcalendar_plugin_display_page extends views_plugin_display_page {
  function render() {
    if ($this->view->total_rows == 0) {
      $this->view->result[] = new stdClass();
      $this->view->empty = $this->render_textarea('empty');
    }
    return parent::render();
  }
}
