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

  public function process(&$variables, &$settings);

  public function buildOptionsForm(&$form, &$form_state);

}
