<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\FullcalendarBase.
 */

namespace Drupal\fullcalendar\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\fullcalendar\Plugin\FullcalendarInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;

/**
 * @todo.
 */
abstract class FullcalendarBase extends PluginBase implements FullcalendarInterface {

  /**
   * @todo.
   *
   * @var \Drupal\views\Plugin\views\style\StylePluginBase
   */
  protected $style;

  /**
   * Constructs a Fullcalendar object.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, StylePluginBase $style) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->style = $style;
  }

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::submitOptionsForm().
   */
  public function submitOptionsForm(&$form, &$form_state) {
  }

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::process().
   */
  public function process(&$settings) {
  }

  /**
   * Implements \Drupal\fullcalendar\Plugin\FullcalendarInterface::preView().
   */
  public function preView(&$settings) {
  }

}
