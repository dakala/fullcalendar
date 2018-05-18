<?php

namespace Drupal\fullcalendar\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo.
 */
class UpdateController extends ControllerBase {

  /**
   * @todo.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param string $field
   * @param int $index
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function drop(EntityInterface $entity, $field, $index, Request $request) {
    if ($request->request->has('day_delta') && $request->request->has('minute_delta')) {
      $day_delta = Html::escape($request->request->get('day_delta'));
      $minute_delta = Html::escape($request->request->get('minute_delta'));
      $delta = " $day_delta days $minute_delta minutes";

      $field_item = $entity->{$field}->get($index);
      $value = $field_item->value;
      $field_item->set('value', date(DATETIME_DATETIME_STORAGE_FORMAT, strtotime($value . $delta)));

      // Save the new start/end values.
      $entity->save();

      $url = Url::fromUserInput('/');
      $link = Link::fromTextAndUrl($this->t('Close'), $url);
      $link = $link->toRenderable();
      $link['#attributes']['class'][] = 'fullcalendar-status-close';

      $message = $this->t('The new event time has been saved.');
      $message .= ' [' . \Drupal::service('renderer')->render($link) . ']';
    }
    else {
      $message = $this->t('The event has not been updated.');
    }

    return new JsonResponse([
      'msg'    => $message,
      'dom_id' => $request->request->get('dom_id'),
    ]);
  }

}
