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
  public function __construct(array $configuration, $plugin_id, DiscoveryInterface $discovery, StylePluginBase $style) {
    parent::__construct($configuration, $plugin_id, $discovery);

    $this->style = $style;
  }

}
