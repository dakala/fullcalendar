<?php

/**
 * @file
 * Contains \Drupal\fullcalendar_options\FullcalendarOptionsBundle.
 */

namespace Drupal\fullcalendar_options;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FullCalendar Options dependency injection container.
 */
class FullcalendarOptionsBundle extends Bundle {

  /**
   * Overrides \Symfony\Component\HttpKernel\Bundle\Bundle::build().
   */
  public function build(ContainerBuilder $container) {
    $container->register('fullcalendar_options.form.settings', 'Drupal\fullcalendar_options\Form\SettingsForm')
      ->addArgument(new Reference('config.factory'))
      ->addArgument(new Reference('plugin.manager.fullcalendar'));
  }

}
