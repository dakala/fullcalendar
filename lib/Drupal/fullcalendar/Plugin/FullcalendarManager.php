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
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   */
  public function __construct(\Traversable $namespaces) {
    $annotation_namespaces = array('Drupal\fullcalendar\Annotation' => $namespaces['Drupal\fullcalendar']);
    $this->discovery = new AnnotatedClassDiscovery('fullcalendar/type', $namespaces, $annotation_namespaces, 'Drupal\fullcalendar\Annotation\FullcalendarOption');

    $this->factory = new DefaultFactory($this->discovery);

  }

  /**
   * Overrides \Drupal\Component\Plugin\PluginManagerBase::createInstance().
   *
   * Pass the TipsBag to the plugin constructor.
   */
  public function createInstance($plugin_id, array $configuration = array(), $style = NULL) {
    $plugin_definition = $this->discovery->getDefinition($plugin_id);
    $plugin_class = DefaultFactory::getPluginClass($plugin_id, $plugin_definition);
    return new $plugin_class($configuration, $plugin_id, $plugin_definition, $style);
  }

}
