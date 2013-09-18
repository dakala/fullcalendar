<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\FullcalendarManager.
 */

namespace Drupal\fullcalendar\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Plugin type manager for FullCalendar plugins.
 */
class FullcalendarManager extends DefaultPluginManager {

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
    parent::__construct('Plugin/fullcalendar/type', $namespaces, 'Drupal\fullcalendar\Annotation\FullcalendarOption');
  }

  /**
   * Overrides \Drupal\Component\Plugin\PluginManagerBase::createInstance().
   *
   * Pass the TipsBag to the plugin constructor.
   */
  public function createInstance($plugin_id, array $configuration = array(), $style = NULL) {
    $plugin = parent::createInstance($plugin_id, $configuration);
    $plugin->setStyle($style);
    return $plugin;
  }

}
