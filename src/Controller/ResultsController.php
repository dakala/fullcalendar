<?php

namespace Drupal\fullcalendar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\fullcalendar\Ajax\ResultsCommand;
use Drupal\views\Entity\View;
use Drupal\Core\Ajax\AjaxResponse;

/**
 * Controller for handling ajax requests.
 */
class ResultsController extends ControllerBase {

  /**
   * Ajax callback to refresh calendar view.
   *
   * @param \Drupal\views\Entity\View $view
   *   Fully-loaded view entity.
   * @param string $display_id
   *   Display ID.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|void
   */
  public function getResults(View $view, $display_id) {
    $response = new AjaxResponse();

    if (!$view) {
      return $response;
    }

    $view = $view->getExecutable();

    if (!$view->access($display_id)) {
      return $response;
    }

    if (!$view->setDisplay($display_id)) {
      return $response;
    }

    $request = \Drupal::request();

    $args = $request->request->get('view_args', '');
    $args = explode('/', $args);

    $view->setExposedInput($request->request->all());
    $view->preExecute($args);
    $view->execute($display_id);
    $content = $view->buildRenderable($display_id, $args);

    $rendered = \Drupal::service('renderer')->renderRoot($content);
    $response->addCommand(new ResultsCommand($rendered));

    return $response;
  }

}
