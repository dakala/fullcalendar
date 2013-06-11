<?php

/**
 * @file
 * Contains \Drupal\fullcalendar\Ajax\Update.
 */

namespace Drupal\fullcalendar\Ajax;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo.
 */
class Update {

  protected $entity;
  protected $langcode;
  protected $delta;
  protected $format = DATETIME_DATETIME_STORAGE_FORMAT;

  /**
   * @todo.
   */
  protected function prepare($entity_type, $entity_id, $field, Request $request) {
    $this->entity = entity_load($entity_type, $entity_id);
    $this->langcode = field_language($this->entity, $field);

    $day_delta = check_plain($request->request->get('day_delta'));
    $minute_delta = check_plain($request->request->get('minute_delta'));
    $this->delta = " $day_delta days $minute_delta minutes";
  }

  /**
   * @todo.
   */
  public function drop($entity_type, $entity_id, $field, $index, Request $request) {
    // @todo Remove once http://drupal.org/node/1915752 is resolved.
    $index--;

    $this->prepare($entity_type, $entity_id, $field, $request);
    $item = &$this->entity->{$field}[$this->langcode][$index];
    $item['value'] = date($this->format, strtotime($item['value'] . $this->delta));

    // Save the new start/end values.
    $this->entity->save();
    $message = t('The new event time has been saved.') .  ' [' . l(t('Close'), NULL, array('attributes' => array('class' => array('fullcalendar-status-close')))) . ']';
    return new JsonResponse(array(
      'msg' => $message,
      'dom_id' => $request->request->get('dom_id'),
    ));
  }

}
