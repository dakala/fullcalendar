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
    if (empty($this->view->result)) {
      $this->view->result[] = new stdClass();
      $this->view->empty_text = $this->render_area('empty');
    }
    return parent::render();
  }
}
