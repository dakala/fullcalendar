<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\FullcalendarManager.
 */

namespace Drupal\fullcalendar\Plugin;

use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Plugin\Discovery\ProcessDecorator;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;

/**
 * Plugin type manager for FullCalendar plugins.
 */
class FullcalendarManager extends PluginManagerBase {

  /**
   * @todo.
   */
  protected $defaults = array(
    'css' => FALSE,
    'js' => FALSE,
  );

  /**
   * Constructs a FullcalendarManager object.
   *
   * @param array $namespaces
   *   An array of paths keyed by it's corresponding namespaces.
   */
  public function __construct(array $namespaces = array()) {
    $this->discovery = new AnnotatedClassDiscovery('fullcalendar', 'type', $namespaces);
    $this->discovery = new ProcessDecorator($this->discovery, array($this, 'processDefinition'));

    $this->factory = new DefaultFactory($this->discovery);

  }

  /**
   * Overrides \Drupal\Component\Plugin\PluginManagerBase::createInstance().
   *
   * Pass the TipsBag to the plugin constructor.
   */
  public function createInstance($plugin_id, array $configuration = array(), $style = NULL) {
    $plugin_class = DefaultFactory::getPluginClass($plugin_id, $this->discovery);
    return new $plugin_class($configuration, $plugin_id, $this->discovery, $style);
  }

}
