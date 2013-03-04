<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Access\UpdateAccessCheck.
 */

namespace Drupal\fullcalendar\Access;

use Drupal\Core\Access\AccessCheckInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;

/**
 * @todo.
 */
class UpdateAccessCheck implements AccessCheckInterface {

  /**
   * Implements \Drupal\Core\Access\AccessCheckInterface::applies().
   */
  public function applies(Route $route) {
    return array_key_exists('_access_fullcalendar_update', $route->getRequirements());
  }

  /**
   * Implements \Drupal\Core\Access\AccessCheckInterface::access().
   */
  public function access(Route $route, Request $request) {
    $entity_type = $request->attributes->get('entity_type');
    $entity_id = $request->attributes->get('entity_id');
    $entity = entity_load($entity_type, $entity_id);
    return $this->check($entity);
  }

  /**
   * @todo.
   */
  public function check(EntityInterface $entity) {
    global $user;
    if (!empty($entity) && ((user_access('administer content')
        || user_access('update any fullcalendar event')
        || user_access('edit any ' . $entity->bundle() . ' content')
        || (user_access('edit own ' . $entity->bundle() . ' content') && $entity->uid == $user->uid)))) {
      return TRUE;
    }
    return FALSE;
  }

}
