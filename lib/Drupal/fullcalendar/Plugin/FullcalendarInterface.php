<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\FullcalendarInterface.
 */

namespace Drupal\fullcalendar\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * @todo.
 */
interface FullcalendarInterface {

  public function setStyle(StylePluginBase $style);

  public function defineOptions();

  public function buildOptionsForm(&$form, FormStateInterface $form_state);

  public function submitOptionsForm(&$form, FormStateInterface $form_state);

  public function process(&$settings);

  public function preView(&$settings);

}
