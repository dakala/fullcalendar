<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\FullcalendarBundle.
 */

namespace Drupal\fullcalendar;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FullCalendar dependency injection container.
 */
class FullcalendarBundle extends Bundle {

  /**
   * Overrides \Symfony\Component\HttpKernel\Bundle\Bundle::build().
   */
  public function build(ContainerBuilder $container) {
    $container->register('plugin.manager.fullcalendar', 'Drupal\fullcalendar\Plugin\FullcalendarManager')
      ->addArgument('%container.namespaces%');
  }

}
