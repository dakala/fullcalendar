<?php

namespace Drupal\fullcalendar\Ajax;

use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\views\ViewExecutable;

/**
 * @todo.
 */
class ResultsController {

  /**
   * @todo.
   *
   * @param \Drupal\views\ViewExecutable $view
   * @param $display_id
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|void
   */
  public function getResults(ViewExecutable $view, $display_id, Request $request) {
    $response = new AjaxResponse();

    if (!$view || !$view->access($display_id)) {
      return $response;
    }

    if (!$view->setDisplay($display_id)) {
      return $response;
    }

    $args = [];
    $view->dom_id = $request->request->get('dom_id');
    $view->ajax = TRUE;
    $view->preExecute($args);
    $view->initStyle();
    $view->execute($display_id);
    $output = $view->style_plugin->render();
    $view->postExecute();

    $rendered = \Drupal::service('renderer')->render($output);
    $response->addCommand(new ResultsCommand($rendered));

    return $response;
  }

}
