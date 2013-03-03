<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\FullcalendarInterface.
 */

namespace Drupal\fullcalendar\Plugin;

/**
 * @todo.
 */
interface FullcalendarInterface {

  public function buildOptionsForm(&$form, &$form_state);

  public function submitOptionsForm(&$form, &$form_state);

  public function process(&$variables, &$settings);

  public function preview(&$settings);

}
