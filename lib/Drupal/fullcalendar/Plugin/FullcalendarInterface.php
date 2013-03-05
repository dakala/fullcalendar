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

  public function defineOptions();

  public function buildOptionsForm(&$form, &$form_state);

  public function submitOptionsForm(&$form, &$form_state);

  public function process(&$settings);

  public function preView(&$settings);

}
