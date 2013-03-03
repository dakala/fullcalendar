<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Plugin\views\style\FullCalendar.
 */

namespace Drupal\fullcalendar\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\fullcalendar\Plugin\FullcalendarPluginBag;

/**
 * @todo.
 *
 * @Plugin(
 *   id = "fullcalendar",
 *   title = @Translation("FullCalendar"),
 *   help = @Translation("Displays items on a calendar."),
 *   module = "fullcalendar",
 *   theme = "fullcalendar",
 *   theme_file = "theme.inc",
 *   theme_path = "theme",
 *   type = "normal"
 * )
 */
class FullCalendar extends StylePluginBase {

  /**
   * @todo.
   */
  protected $usesFields = TRUE;

  /**
   * @todo
   *
   * @var \Drupal\fullcalendar\Plugin\FullcalendarPluginBag
   */
  protected $pluginBag;

  /**
   * @todo.
   */
  public function even_empty() {
    return TRUE;
  }

  public function __construct(array $configuration, $plugin_id, DiscoveryInterface $discovery) {
    parent::__construct($configuration, $plugin_id, $discovery);

    $this->pluginBag = new FullcalendarPluginBag(drupal_container()->get('plugin.manager.fullcalendar'), $this);
  }

  /**
   * @todo.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    foreach ($this->pluginBag as $plugin) {
      $options += $plugin->defineOptions();
    }
    return $options;
  }

  /**
   * @todo.
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);
    foreach ($this->pluginBag as $plugin) {
      $plugin->buildOptionsForm($form, $form_state);
    }
  }

  /**
   * @todo.
   */
  public function parseFields($include_gcal = TRUE) {
    $this->view->initHandlers();
    $labels = $this->displayHandler->getFieldLabels();
    $date_fields = array();
    foreach ($this->view->field as $id => $field) {
      if (fullcalendar_field_is_date($field, $include_gcal)) {
        $date_fields[$id] = $labels[$id];
      }
    }
    return $date_fields;
  }

}
