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

  function validate() {
    $errors = parent::validate();

    $field_options = $this->display->handler->get_field_labels();
    $date_fields = fullcalendar_date_fields($field_options);

    if (empty($date_fields)) {
      $errors[] = t('Display "@display" requires at least one date field.', array('@display' => $this->display->display_title));
    }

    return $errors;
  }
}
